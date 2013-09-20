<?php namespace RobinMalfait\Formgenerator;

use Illuminate\Html\FormBuilder as Form;

class Formgenerator{

    protected $settings;
    protected $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function generate($model, $options = array())
    {
        $fields = array();
        if ( ! is_object($model)){
            $table      = $model;
            $fields     = $this->getFields($model);
        } else {
            $table      = $model->getTable();
            $fields     = $model->toArray();
        }

        $columns    = \DB::getDoctrineSchemaManager()->listTableDetails($table)->getColumns();

        $this->setSettings($options);

        /**
         * Loop trought all the fields from the model
         */
        foreach ($fields as $fieldName => $value) {

            $value = (isset($value) AND !empty($value)) ? $value : false;

            $extras = $this->getSettings('extras', $fieldName);

            /**
             * Check for wildcards: *
             */
            if (empty($extras)) {
                $extras = $this->getSettings('extras', '*');
            }

            if ( ! in_array($fieldName, $this->getSettings('exclude'))) {
                $type = $this->getSettings('types', $fieldName);

                if ( ! empty($type)) {
                    if (isset($type['type'])) {
                        $type = $type['type'];
                    }
                }


                if ( ! isset($type) OR empty($type)) {
                    $dataType = $columns[$fieldName]->getType()->getName();
                    $type = $this->getInputType($dataType, $fieldName);
                }

                switch ($type) {
                    case 'checkbox':
                        if ($this->getContentBefore($fieldName))
                            $data[] = $this->getContentBefore($fieldName);

                        if ($this->getSettings('showLabels')) {
                            $data[] = "<label class='checkbox'>";
                        }
                        $data[] = $this->form->checkbox($fieldName, null, null, $extras) . $this->getLabelText($fieldName);
                        if ($this->getSettings('showLabels')) {
                            $data[] = "</label>";
                        }

                        if ($this->getContentAfter($fieldName))
                            $data[] = $this->getContentAfter($fieldName);

                        break;

                    case 'radio':
                        if ($this->getContentBefore($fieldName))
                            $data[] = $this->getContentBefore($fieldName);

                        if ($this->getSettings('showLabels')) {
                            $data[] = "<label class='radio'>";
                        }
                        $data[] = $this->form->radio($fieldName, null, null, $extras) . $this->getLabelText($fieldName);
                        if ($this->getSettings('showLabels')) {
                            $data[] = "</label>";
                        }

                        if ($this->getContentAfter($fieldName))
                            $data[] = $this->getContentAfter($fieldName);

                        break;

                    case 'date':
                        if ($this->getContentBefore($fieldName))
                            $data[] = $this->getContentBefore($fieldName);

                        if ($this->getSettings('showLabels')) {
                            $data[] = $this->form->label($fieldName, $this->getLabelText($fieldName) . ':');
                        }
                        $data[] = $this->form->input('date', $fieldName, date('Y-m-d', strtotime($value)), $extras);

                        if ($this->getContentAfter($fieldName))
                            $data[] = $this->getContentAfter($fieldName);

                        break;

                    case 'time':
                        if ($this->getContentBefore($fieldName))
                            $data[] = $this->getContentBefore($fieldName);

                        if ($this->getSettings('showLabels')) {
                            $data[] = $this->form->label($fieldName, $this->getLabelText($fieldName) . ':');
                        }
                        $data[] = $this->form->input('time', $fieldName, date('H:i:s', strtotime($value)), $extras);

                        if ($this->getContentAfter($fieldName))
                            $data[] = $this->getContentAfter($fieldName);

                        break;

                    case 'textarea':
                        if ($this->getContentBefore($fieldName))
                            $data[] = $this->getContentBefore($fieldName);

                        if ($this->getSettings('showLabels')) {
                            $data[] = $this->form->label($fieldName, $this->getLabelText($fieldName) . ':');
                        }
                        $data[] = $this->form->textarea($fieldName, null, $extras);

                        if ($this->getContentAfter($fieldName))
                            $data[] = $this->getContentAfter($fieldName);

                        break;

                    case 'select':
                        if ($this->getContentBefore($fieldName))
                            $data[] = $this->getContentBefore($fieldName);

                        if ($this->getSettings('showLabels')) {
                            $data[] = $this->form->label($fieldName, $this->getLabelText($fieldName) . ':');
                        }

                        $data[] = $this->form->select($fieldName, $this->getSettings('types', $fieldName, 'options'), null, $extras);

                        if ($this->getContentAfter($fieldName))
                            $data[] = $this->getContentAfter($fieldName);

                        break;

                    default:
                        if ($this->getContentBefore($fieldName))
                            $data[] = $this->getContentBefore($fieldName);

                        if ($this->getSettings('showLabels')) {
                            $data[] = $this->form->label($fieldName, $this->getLabelText($fieldName) . ':');
                        }

                        $data[] = $this->form->input($type, $fieldName, null, $extras);

                        if ($this->getContentAfter($fieldName))
                            $data[] = $this->getContentAfter($fieldName);

                        break;
                }
            }

        }

        /**
         * Check if we need to show the submit button
         */
        if ($this->getSettings('submit', 'show')) {
            $data[] = $this->form->label('submit', '&nbsp;'); // get some space above the button
            $data[] = $this->form->submit($this->getSettings('submit', 'text'), array('class' => $this->getSettings('submit', 'class')));
        }

        return trim(implode(PHP_EOL, $data));
    }

    protected function getFields($table)
    {
        $field_names = array();
        $columns = \DB::select("SHOW COLUMNS FROM `" . strtolower($table) . "`");
        foreach ($columns as $c) {
            $field = $c->Field;
            $field_names[$field] = $field;
        }

        return $field_names;
    }

    protected function getLabelText($fieldName)
    {
        $label = $this->getSettings('extras', $fieldName, 'label');
        if (isset($label) AND !empty($label)) {
            return $this->getSettings('extras', $fieldName, 'label');
        }
        return ucwords(str_replace("_", " ", $fieldName));
    }

    protected function getContentBefore($fieldName)
    {
        $content = $this->getSettings('extras', $fieldName, 'content_before');
        if (isset($content) AND !empty($content)) {
            return $this->getSettings('extras', $fieldName, 'content_before');
        }
    }

    protected function getContentAfter($fieldName)
    {
        $content = $this->getSettings('extras', $fieldName, 'content_after');
        if (isset($content) AND !empty($content)) {
            return $this->getSettings('extras', $fieldName, 'content_after');
        }
    }

    protected function getInputType($dataType, $name)
    {
        $lookup = array(
            'string'  => 'text',
            'float'   => 'text',
            'date'    => 'text',
            'text'    => 'textarea',
            'boolean' => 'checkbox'
        );

        return array_key_exists($dataType, $lookup)
            ? $lookup[$dataType]
            : 'text';
    }

    /**
     * set the settings
     * @param array $options options set by the user in the form itself
     */
    protected function setSettings($options)
    {
        $settings = array(
            'exclude'       => array(
                'id',
                'created_at',
                'updated_at',
                'deleted_at',
                'password'
            ),
            'showLabels'    => true,
            'types'         => array(
                'password'  => 'password',
                'email'     => 'email'
            ),
            'submit'        => array(
                'show'      => true,
                'text'      => 'Submit',
                'class'     => 'btn btn-success',
            )
        );

        $this->settings = array_merge($settings, $options);
    }

    /**
     * get the settings
     * @return array settings
     */
    protected function getSettings()
    {
        $stngs = $this->settings;
        foreach (func_get_args() as $arg) {
            if ( ! is_array($stngs) OR ! is_scalar($arg) OR ! isset($stngs[$arg])) {
                return array();
            }
            $stngs = $stngs[$arg];
        }
        return $stngs;
    }
}
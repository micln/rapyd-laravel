<?php

namespace Zofe\Rapyd\DataForm\Field;

use Collective\Html\FormFacade as Form;
use Illuminate\Support\Facades\Input;
use Zofe\Rapyd\Rapyd;

class Daterange extends Date
{
    public $type = "daterange";
    public $multiple = true;
    public $clause = "wherebetween";

    protected $suffix_from = '-from';
    protected $suffix_to = '-to';

    public function getNewValue()
    {
        $this->values = [];

        $origin = $this->name;
        $this->name = $origin. $this->suffix_from;
        $this->new_value = null;
        Field::getNewValue();
        $this->values [] = $this->new_value;

        $this->name = $origin. $this->suffix_to;
        $this->new_value = null;
        Field::getNewValue();
        $this->values [] = $this->new_value;

        $this->name = $origin;

        foreach ($this->values as $value) {
            $values[] = $this->humanDateToIso($value);
        }

        if (isset($values)) {
            $this->new_value = implode($this->serialization_sep, $values);
        }
    }


    public function getValue()
    {
        $this->values = [];

        $origin = $this->name;
        $this->name = $origin. $this->suffix_from;
        $this->new_value = null;
        Field::getValue();
        $this->values [] = $this->value;

        $this->name = $origin. $this->suffix_to;
        $this->new_value = null;
        Field::getValue();
        $this->values [] = $this->value;

        $this->name = $origin;

        foreach ($this->values as $value) {
            $values[] = $this->isoDateToHuman($value);
        }
        if (isset($values)) {
            $this->value = implode($this->serialization_sep, $values);
        }
    }
    
    public function build()
    {
        $output = "";

        unset($this->attributes['type']);
        if (parent::build() === false) return;
        
        switch ($this->status) {

            case "show":
                if (!isset($this->value)) {
                    $value = $this->layout['null_label'];
                } else {
                    $value = str_replace($this->serialization_sep, ' ', $this->value);
                }
                $output = $value;
                $output = "<div class='help-block'>" . $output . "&nbsp;</div>";
                break;

            case "create":
            case "modify":


                Rapyd::css('datepicker/datepicker3.css');
                Rapyd::js('datepicker/bootstrap-datepicker.js');
                if ($this->language != "en") {
                    Rapyd::js('datepicker/locales/bootstrap-datepicker.' . $this->language . '.js');
                }

                unset($this->attributes['id']);
                //$this->attributes['class'] = "form-control";

                $from = Form::text($this->name . $this->suffix_from, @$this->values[0], $this->attributes);
                $to = Form::text($this->name . $this->suffix_to, @$this->values[1], $this->attributes);


                $output = '
                            <div id="range_' . $this->name . '_container">
                                <div class="input-daterange">
                                   <div class="input-group">
                                       <div class="input-group-addon">&ge;</div>
                                       ' . $from . '
                                   </div>
                                   <div class="input-group">
                                        <div class="input-group-addon">&le;</div>
                                        ' . $to . '
                                   </div>
                                </div>
                            </div>';

                Rapyd::pop_script();
                Rapyd::script("
                        $('#range_{$this->name}_container .input-daterange').datepicker({
                            format: '{$this->formatToDate()}',
                            language: '{$this->language}',
                            todayBtn: 'linked',
                            autoclose: true
                        });");

                break;
            case "hidden":
                $output = Form::hidden($this->name, $this->value);
                break;
            default:
                ;
        }
        $this->output = $output;
    }

}

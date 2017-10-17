<?php

namespace Zofe\Rapyd\DataForm\Field;

use Collective\Html\FormFacade as Form;
use Zofe\Rapyd\Rapyd;

class Numberrange extends Number
{
    public $type = "numberrange";
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

        $this->new_value = implode($this->serialization_sep, $this->values);
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

        $this->value = implode($this->serialization_sep, $this->values);
    }

    public function build()
    {
        $output = "";

        if (parent::build() === false) {
            return;
        }

        switch ($this->status) {
            case "disabled":
            case "show":

                if ($this->type == 'hidden' || $this->value === "") {
                    $output = "";
                } elseif ((!isset($this->value))) {
                    $output = $this->layout['null_label'];
                } else {
                    $output = $this->value;
                }
                $output = "<div class='help-block'>" . $output . "&nbsp;</div>";
                break;

            case "create":
            case "modify":

                $lower = Form::number($this->name . $this->suffix_from, @trim($this->values[0],$this->serialization_sep), $this->attributes);
                $upper = Form::number($this->name . $this->suffix_to, @trim($this->values[1],$this->serialization_sep), $this->attributes);

                $output = '
                            <div id="range_' . $this->name . '_container">
                                   <div class="input-group">
                                       <div class="input-group-addon">&ge;</div>
                                       ' . $lower . '
                                   </div>
                                   <div class="input-group">
                                        <div class="input-group-addon">&le;</div>
                                        ' . $upper . '
                                   </div>
                            </div>';
                break;

            case "hidden":
                $output = Form::hidden($this->name, $this->value);
                break;

            default:
        }
        $this->output = "\n" . $output . "\n" . $this->extra_output . "\n";
    }

}

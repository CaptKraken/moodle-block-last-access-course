<?php

/**
 * Contains the class for the Last Access Course block.
 *
 * @package    block_last_access_course
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author CaptKraken
 */

class block_last_access_course_edit_form extends block_edit_form
{

    protected function specific_definition($mform)
    {

        /**
         * generates a color picker with a reset button all with javascript. styled with the css code below this function.
         * 
         * @param string $id used to create unique classes and ids for the elements.
         * @param string $defaultClr default color for the button to reset to.
         * @return javascipt-code
         */
        function color_picker_generator($id, $defaultClr)
        {
            //still putting this here because i dont know how to do dynamic variable naming in js
            echo "
            <script>
                const {$id}Picker = `<div style='display: flex; align-items: center;'><input type='color' id='id_config_input_{$id}_picker'></div>`;
                const btn{$id}Reset = `<div id='btn_{$id}_reset' class='btn-reset-clr'>Reset Color</div>`;
                
                window.addEventListener('load', (event) => {
                    const {$id} = document.querySelector('#fitem_id_config_{$id}');
                    const {$id}TextBox = document.querySelector('#id_config_{$id}');
                    
                    if (!{$id}TextBox) return;
                    {$id}TextBox.style.display='none';
                    {$id}TextBox.insertAdjacentHTML('afterend', btn{$id}Reset);
                    {$id}TextBox.insertAdjacentHTML('afterend', {$id}Picker);
                    const {$id}El = document.querySelector('#id_config_input_{$id}_picker');
                    {$id}El.value = {$id}TextBox.value;
                    {$id}El.addEventListener('input', ()=>{
                        {$id}TextBox.value = {$id}El.value;
                    });                   

                    const btnReset{$id}Clr = document.querySelector('#btn_{$id}_reset');
                    btnReset{$id}Clr.addEventListener('click', ()=>{
                        {$id}El.value = '{$defaultClr}';
                        {$id}TextBox.value = '{$defaultClr}';
                    });
                });
            </script>";
        } ?>

        <script>
            window.addEventListener('load', () => {
                const configCardHeader = document.querySelector('#id_config_card_header');
                const configButtonHeader = document.querySelector('#id_config_btn_header');
                //show both setting (default is to show only one setting block)
                [configCardHeader, configButtonHeader].forEach(confHead => confHead.classList.remove('collapsed'));
            });
        </script>

        <style>
            .form-group.row {
                align-items: center;
            }

            .btn-reset-clr {
                height: 23px;
                padding: 0 1rem;
                margin: 0;
                margin-left: 1rem;
                cursor: pointer;
                background: #a9291b;
                color: whitesmoke;
                transition: all .25s;
            }

            .btn-reset-clr:hover,
            .btn-reset-clr:focus {
                filter: brightness(1.25);
            }
        </style>
<?php

        //CARD SETTINGS
        $mform->addElement('header', 'config_card_header', get_string('config_card', 'block_last_access_course'));

        //time elapsed
        $mform->addElement('advcheckbox', 'config_show_time_elapsed', get_string('config_show_time_elapsed', 'block_last_access_course'), 'Show', array('group' => 1), array(0, 1));

        //number of courses to show
        $options = array();
        for ($i = 0; $i <= 10; $i++) {
            $options[$i] = $i;
        }
        $options[count($options) + 1] = 999; //sets 999 as the last option

        $mform->addElement(
            'select',
            'config_course_number',
            get_string('config_course_number', 'block_last_access_course'),
            $options
        );
        $mform->setDefault('config_course_number', 3);
        $mform->setType('course_number', PARAM_INT);
        $mform->addElement('static', 'course_number_note', '', '0 = reset to default (3)<br>999 = show all');

        //thumbnail bg color
        $mform->addElement('text', 'config_thumb_background_color', get_string('config_thumb_background_color', 'block_last_access_course'));
        $mform->setDefault('config_thumb_background_color', '#c3c3c3');
        $mform->setType('thumb_background_color', PARAM_RAW);
        color_picker_generator('thumb_background_color', '#c3c3c3');

        //thumbnail txt color
        $mform->addElement('text', 'config_thumb_text_color', get_string('config_thumb_text_color', 'block_last_access_course'));
        $mform->setDefault('config_thumb_text_color', '#f1f1f1');
        $mform->setType('thumb_text_color', PARAM_RAW);
        color_picker_generator('thumb_text_color', '#f1f1f1');

        //BUTTON SETTINGS
        $mform->addElement('header', 'config_btn_header', get_string('config_btn', 'block_last_access_course'));

        //btn background color
        $mform->addElement('text', 'config_background', get_string('config_btn_background', 'block_last_access_course'));
        $mform->setDefault('config_background', '#f1f1f1');
        $mform->setType('btn_background', PARAM_RAW);
        color_picker_generator('background', '#f1f1f1');

        //btn text color
        $mform->addElement('text', 'config_color', get_string('config_btn_text', 'block_last_access_course'));
        $mform->setDefault('config_color', '#030303');
        $mform->setType('btn_color', PARAM_RAW);
        color_picker_generator('color', '#030303');
    }
}

<?php
class block_last_access_course_edit_form extends block_edit_form
{

    protected function specific_definition($mform)
    {

        function color_picker_generator($id, $defaultClr){
        echo "
        <script>
            let {$id}, {$id}TextBox;
            const {$id}Picker = `<div style='display: flex; align-items: center;'><input type='color' id='id_config_input_{$id}_picker'></div>`;
            const btn{$id}Reset = `<div id='btn_{$id}_reset' class='btn-reset-clr'>Reset Color</div>`;
            
            window.addEventListener('load', (event) => {
                {$id} = document.querySelector('#fitem_id_config_{$id}');
                {$id}TextBox = document.querySelector('#id_config_{$id}');
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
                    {$id}El.value = '#{$defaultClr}';
                    {$id}TextBox.value = '#{$defaultClr}';
                    console.log({$id}El.value, {$id}TextBox.value);
                });
            });
        
        </script>";
        }

echo"
    <script>
        
    </script>
";

echo "<style>.form-group.row {
    align-items: center;
}
.btn-reset-clr{
    height: 23px;
    padding: 0 1rem; 
    margin: 0; 
    margin-left:1rem;
    cursor: pointer;
    background: #a9291b;
    color: whitesmoke;
    transition: all .25s;
}
.btn-reset-clr:hover, .btn-reset-clr:focus{
    filter: brightness(1.25);
}
</style>";

        $protocol = !empty($_SERVER['HTTPS'])?'https://':'http://';
        $site_url= $protocol.$_SERVER['SERVER_NAME'];

        $mform->addElement('header', 'config_block_header', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_moodle_dir', 'Moodle Installation Directory');
        $mform->setDefault('config_moodle_dir', $site_url);
        $mform->setType('site_url', PARAM_RAW);
        $mform->addElement('static', 'moodle_dir_note', '', 'this is for the link to the course. usually, the link to a course looks like: [moodle_directory]/course/view.php?id=1 <br> to get the url, view a course page and copy the url in the address bar without the "/course/view.php?id=1"');
        
        $options = array();
        for ($i = 0; $i <= 10; $i++) {
            $options[$i] = $i;
        }
        $options[count($options) + 1] = 999;
        
        $mform->addElement(
            'select',
            'config_course_number',
            get_string('config_course_number', 'block_last_access_course'),
            $options
        );
        $mform->setDefault('config_course_number', 3);
        $mform->setType('course_number', PARAM_INT);
        $mform->addElement('static', 'course_number_note', '', '0 = reset to default (3)<br>999 = show all');
        
        $mform->addElement('text', 'config_thumb_color', get_string('config_thumb_color', 'block_last_access_course'));
        $mform->setDefault('config_thumb_color', '#c3c3c3');
        $mform->setType('thumb_color', PARAM_RAW);
        color_picker_generator('thumb_color', 'c3c3c3');
        
        
        

        // BUTTON SETTINGS
        $mform->addElement('header', 'config_btn_header', get_string('config_btn', 'block_last_access_course'));
        
        $mform->addElement('text', 'config_background', get_string('config_btn_background', 'block_last_access_course'));
        $mform->setDefault('config_background', '#f1f1f1');
        $mform->setType('btn_background', PARAM_RAW);
        color_picker_generator('background', 'f1f1f1');

        $mform->addElement('text', 'config_color', get_string('config_btn_text', 'block_last_access_course'));
        $mform->setDefault('config_color', '#030303');
        $mform->setType('btn_color', PARAM_RAW);
        color_picker_generator('color', '030303');
    }
}
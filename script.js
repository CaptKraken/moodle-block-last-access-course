const <?=$id?>Picker = `<div style='display: flex; align-items: center;'><input type='color' id='id_config_input_<?=$id?>_picker'></div>`;

const <?=$id?> = document.querySelector('#fitem_id_config_<?=$id?>');
const <?=$id?>TextBox = document.querySelector('#id_config_<?=$id?>');

<?=$id?>TextBox.style.display='none';
<?=$id?>TextBox.insertAdjacentHTML('afterend', <?=$id?>Picker);
const <?=$id?>El = document.querySelector('#id_config_input_<?=$id?>_picker');
<?=$id?>El.value = <?=$id?>TextBox.value;
<?=$id?>El.addEventListener('input', ()=>{
    <?=$id?>TextBox.value = <?=$id?>El.value;
});

const <?=$id?>paraEl = document.createElement('p');
<?=$id?>paraEl.setAttribute('id', 'btn_".$id."_reset');
<?=$id?>paraEl.setAttribute('class', 'btn-reset-clr');
console.log(<?=$id?>El.closest(''));
<?=$id?>El.insertAdjacentHTML('afterend', <?=$id?>paraEl);
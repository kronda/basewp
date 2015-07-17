<h2>Insert Table</h2>
<input type="hidden" name="tve_lb_type" value="tve_table">
<input type="hidden" name="tve_table_style" value="<?php echo $_POST['table_style'] ?>">

<div class="tve_lb_fields">

    <label for="tve_table_rows_number">Number of rows</label>
    <input type="text" name="tve_table_rows" id="tve_table_rows_number" size="20" maxlength="2" value="4"> <label>(1 - 15)</label>

    <div class="tve_field_sep"></div>

    <label for="tve_general_label tve_table_cols_number">Number of columns</label>
    <input type="text" name="tve_table_cols" id="tve_table_cols_number" size="20" maxlength="2" value="4"> <label>(1 - 15)</label>

</div>
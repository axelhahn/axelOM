/*
                       __    __    _______  _______ 
.---.-..--.--..-----.|  |  |__|  |       ||   |   |
|  _  ||_   _||  -__||  |   __   |   -   ||       |
|___._||__.__||_____||__|  |__|  |_______||__|_|__|
                      \\\_____ axels OBJECT MANAGER

----------------------------------------------------------------------

javascript functions for object overview table

----------------------------------------------------------------------
*/

/**
 * initialize datatable with object items and set the sort order
 * by rading html data attribute in the table data-tableorder=\"...\"
 */
function initTableSortorder(){

  const tableId="objlist" // id is defined in apps/admin/pages/object.php

  const table = document.querySelector("#" + tableId);
  const order=table.dataset.tableorder ? JSON.parse(table.dataset.tableorder.replace(/'/g, '"')) : [];

  $('#'+ tableId).DataTable({
    "lengthMenu": [[25, 100, -1], [25, 100, "..."]],
    stateSave: true,
    bSort: true,
    "aaSorting": order
  });
}

// ----------------------------------------------------------------------

$(document).ready(function () {

  initTableSortorder();

});
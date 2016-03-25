$(document).ready(function() {
  jQuery('#up-get-monitors-table').DataTable({
    "aaSorting": [],
    "oLanguage": {
      "sInfo": 'Showing _START_ to _END_ of _TOTAL_ Monitors.',
      "sInfoEmpty": 'No Monitors yet.',
      "sInfoFiltered": 'filtered from _MAX_ total Monitors',
      "sZeroRecords": 'No Monitors Found',
      "sLengthMenu": 'Show _MENU_ Monitors',
      "sEmptyTable": "No Monitors found currently.",
    }
  });

});

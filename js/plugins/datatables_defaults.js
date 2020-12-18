var MSG_DT_FIRST = _("First");
var MSG_DT_LAST = _("Last");
var MSG_DT_NEXT = _("Next");
var MSG_DT_PREVIOUS = _("Previous");
var MSG_DT_EMPTY_TABLE = _("No data available in table");
var MSG_DT_INFO = _("Showing _START_ to _END_ of _TOTAL_");
var MSG_DT_INFO_EMPTY = _("No entries to show");
var MSG_DT_INFO_FILTERED = _("(filtered from _MAX_ total entries)");
var MSG_DT_LENGTH_MENU = _("Show _MENU_ entries");
var MSG_DT_LOADING_RECORDS = _("Loading...");
var MSG_DT_PROCESSING = _("Processing...");
var MSG_DT_SEARCH = _("Search:");
var MSG_DT_ZERO_RECORDS = _("No matching records found");
var CONFIG_EXCLUDE_ARTICLES_FROM_SORT = _("a an the");

var dataTablesDefaults = {
    "language": {
        "paginate": {
            "first"    : window.MSG_DT_FIRST || "First",
            "last"     : window.MSG_DT_LAST || "Last",
            "next"     : window.MSG_DT_NEXT || "Next",
            "previous" : window.MSG_DT_PREVIOUS || "Previous"
        },
        "emptyTable"       : window.MSG_DT_EMPTY_TABLE || "No data available in table",
        "info"             : window.MSG_DT_INFO || "Showing _START_ to _END_ of _TOTAL_ entries",
        "infoEmpty"        : window.MSG_DT_INFO_EMPTY || "No entries to show",
        "infoFiltered"     : window.MSG_DT_INFO_FILTERED || "(filtered from _MAX_ total entries)",
        "lengthMenu"       : window.MSG_DT_LENGTH_MENU || "Show _MENU_ entries",
        "loadingRecords"   : window.MSG_DT_LOADING_RECORDS || "Loading...",
        "processing"       : window.MSG_DT_PROCESSING || "Processing...",
        "search"           : window.MSG_DT_SEARCH || "Search:",
        "zeroRecords"      : window.MSG_DT_ZERO_RECORDS || "No matching records found"
    },
};

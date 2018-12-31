/**
 * File to hold all our select2 related JavaScript
 */

window.select2 = require('select2')

// default all select2 instances to use the bootstrap theme
$.fn.select2.defaults.set( "theme", "bootstrap" );


// views/role/edit.blade.php
$(".js-permission-select").select2({
  width: 'element'
});

// views/gateKeeper/rfidTags/edit.blade.php
$(".select2-basic-single").select2({
});

// views/partials/memberSearch.blade.php
$(".js-data-member-search-ajax").change(function(){
  var user = $(this).val();
  var action = $("#member-search").attr("action").replace("_ID_", user);
  $("#member-search").attr("action", action);
  $("#member-search select[name=user]").attr("disabled", "disabled");
  $("#member-search").submit();
});

$(".js-data-member-search-ajax").select2({
  width: 'element',
  placeholder: "Search for a member...",
  ajax: {
    url: '/api/search/users',
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        page: params.page
      };
    },
    processResults: function (data, params) {
      // need to have a .text value for display
      data.data = $.map(data.data, function (obj) {
              obj.text = obj.fullname;
              return obj;
      });
      // indicate that infinite scrolling can be used
      params.page = params.page || 1;

      return {
        results: data.data,
        pagination: {
          more: (params.page * data.per_page) < data.total
        }
      };
    },
    cache: true
  },
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 1,
  templateResult: formatUser,
  templateSelection: formatUserSelection
});

// views/bankTransactions/edit.blade.php
// views/membership/showDetails.blade.php
$(".js-data-existing-account-ajax").select2({
  placeholder: "Search for a member...",
  dropdownParent: $('.existing-account-select2'),
  ajax: {
    url: '/api/search/users',
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        withAccount: true,
        page: params.page
      };
    },
    processResults: function (data, params) {
      // need to need to have a .text value for display
      // and use the account id as the select value
      data.data = $.map(data.data, function (obj) {
              obj.id = obj.accountId;
              obj.text = obj.fullname;
              return obj;
      });
      
      // indicate that infinite scrolling can be used
      params.page = params.page || 1;

      return {
        results: data.data,
        pagination: {
          more: (params.page * data._per_page) < data.total
        }
      };
    },
    cache: true
  },
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 1,
  templateResult: formatUser,
  templateSelection: formatUserSelection
});

// helpers for the above select2 searches
function formatUser (user) {
  if (user.loading) return user.text;
  var markup = '<div class="membersearch">' +
    '<div class="name"><span class="fullname">' + user.fullname + '</span> <span class="username">' + user.username + '</span></div>' +
    '<div class="email">' + user.email + '</div>' +
    '<div class="address">' + user.address1 + ', ' + user.addressPostcode + '</div>' +
    '<div class="paymentref">' + user.paymentRef + '</div>' +
    '</div>';
  return markup;
}

function formatUserSelection (user) {
  return user.text;
}

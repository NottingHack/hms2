require('./bootstrap');
// This file is for global JavaScript which is to be present on every page.
// It is compiled by Webpack, so can freely contain ES2015 code.

// negstive money to red
$(".money:contains('-')").css("color", "red");

$.ajaxSetup({
   headers: {
     'X-CSRF-Token': document.head.querySelector('meta[name="csrf-token"]').content,
   }
});

$(".js-programmatic-enable").on("click", function () {
  $(".js-data-existing-account-ajax").prop("disabled", false);
});
 
$(".js-programmatic-disable").on("click", function () {
  $(".js-data-existing-account-ajax").prop("disabled", true);
});

$(".js-data-member-search-ajax").change(function(){
  var user = $(this).val();
  var action = $("#member-search").attr("action").replace("_ID_", user);
  $("#member-search").attr("action", action);
  $("#member-search select[name=user]").attr("disabled", "disabled");
  $("#member-search").submit();
});

$(".js-data-member-search-ajax").select2({
  // theme: "foundation",
  width: '100%',
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
  templateResult: formatUser, // omitted for brevity, see the source of this page
  templateSelection: formatUserSelection // omitted for brevity, see the source of this page
});

$(".js-data-existing-account-ajax").select2({
  // theme: "foundation",
  placeholder: "Search for a member...",
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
  templateResult: formatUser, // omitted for brevity, see the source of this page
  templateSelection: formatUserSelection // omitted for brevity, see the source of this page
});

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

// Workaround to reformat sumbited date's into ISO if there in UK format
$("#user-edit-form,#membership-edit-details-form,#register-form").submit(function() {
  var date = $("input[type='date']");
  if (date.val().includes('/')) {
    date.val(date.val().split('/').reverse().join('-'));
  }
});

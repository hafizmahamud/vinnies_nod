/* global bootbox */
/* global initDatatable */
/* global showDialog */
/* global showAlert */
/* global updateUpdatedBy */
/* global diocesan_councils */


(function ($) {
  $(document).ready(function () {
      if ($('.js-table').length) {
          initDatatable({
              table: '.js-table',
              filter: '.js-table-filter',
              search: {
                  form: '.js-table-search-form',
                  input: '.js-table-search-input',
              },
              pagination: {
                  basic: '.pagination-basic',
                  table: '.pagination-table',
              },
              columns: [
                  {
                    data: "country",
                      className: 'text-center',
                  },
                  {
                      data: "beneficiary",
                        className: 'text-center',
                    },
                    {
                      data: "quarter",
                        className: 'text-center',
                    },
                    {
                      data: "year",
                        className: 'text-center',
                    },
                    {
                      data: "total",
                        className: 'text-center',
                    },
                    {
                      data: "projects",
                        className: 'text-center',
                    },
                    {
                      data: "twinning",
                        className: 'text-center',
                    },
                    {
                      data: "grants",
                        className: 'text-center',
                    },
                  
                    {
                      data: "councils",
                        className: 'text-center',
                    },
                    {
                      data: 'download',
                      className: 'download text-center',
                      render: function (data, type, row) {
                        return '<a href="/reports/download/'+ row.year +'/'+row.quarter+'/'+row.id +'"><i class="fa fa-download" aria-hidden="true"></i></a>';
                        },
                  },
              ]
          });
      }
  })
})(jQuery);
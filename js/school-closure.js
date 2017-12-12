/*!
  school-closure JS | © Nick Freear | MIT.
*/

window.jQuery(function ($) {
  'use strict';

  var $widget = $('#school-closure');
  var $conf = $('div[ data-school-closure ]');
  var config = $conf.data('schoolClosure');
  var mSearch = window.location.search.match(/\?s=([\w%]+)/);

  config.m_search = mSearch;
  config.name = config.name || (mSearch ? decodeURIComponent(mSearch[ 1 ]) : null);
  config.tpl = '<a title="Last updated: %t" href="%u"><i>%s </i><b>%oc</b></a>';
  config.url = config.url || './../index.json';

  console.warn('Config:', config, $conf);

  $.getJSON(config.url).done(function (data) {
    console.warn('JSON:', data);

    for (var idx in data.schools) {
      var school = data.schools[ idx ];

      if (school.name === config.name) {
        console.warn('Match:', school);

        var html = config.tpl.replace(/%u/, data.home_url).replace(/%s/, school.name)
            .replace(/%oc/, school.status).replace(/%t/, data.build_time);
        $widget.html(html).addClass(school.status);
        return;
      }
    }

    $widget.html('Not found: ' + config.name).addClass('error-not-found');
  });

  $widget.addClass('school-closure-js');
});

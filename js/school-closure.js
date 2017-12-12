/*!
  © NDF, 12-dec-2017.
*/

window.jQuery(function ($) {
  'use strict';

  var $widget = $('#school-closure');
  var $conf = $('div[ data-school-closure ]');
  var config = $conf.data('schoolClosure');
  var query = window.location.search;
  var m_search = query.match(/\?s=([\w%]+)/);

  config.m_search = m_search;
  config.name = config.name || m_search ? decodeURIComponent(m_search[ 1 ]) : null;
  config.tpl = '<a title="Last updated: %t" href="%u"><i>%s </i><b>%oc</b></a>';
  config.url = config.url || './../index.json';

  console.warn('Config:', config, $conf);
  $widget.addClass('school-closure-js');

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
});

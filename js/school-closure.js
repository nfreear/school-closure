/*!
  © NDF, 12-dec-2017.
*/

jQuery(function ($) {
  'use strict';

  var $widget = $('#school-closure');
  var $conf = $('div[ data-school-closure ]');
  var config = $conf.data( 'schoolClosure' );
  var url = config.url || './../index.json';
  var tpl = '<a title="Last updated: %t" href="%u"><i>%s </i><b>%oc</b></a>';

  console.warn($conf, $conf.data( 'schoolClosure' ), config);

  $.getJSON(url).done(function (data) {
    console.warn('JSON:', data);

    for (var idx in data.schools) {
      var school = data.schools[ idx ];

      if (school.name == config.name) {
        console.warn('Match:', school);

        var html = tpl.replace(/%u/, data.home_url).replace(/%s/, school.name)
          .replace(/%oc/, school.status).replace(/%t/, data.build_time);
        $widget.html(html).addClass('school-closure-js').addClass(school.status); //.attr({ title: 'Last update: ' + data.build_time });
        break;
      }
    }
  });
});

jQuery.jgrid.fluid =
{
  fluidGrid: function(options)
  {
    var grid = $(this);
    var settings = $.extend(
                      {
                        example: grid.closest('.ui-jqgrid').parent(),
                        offset: 0
                      }, options || {});

    var width = $(settings.example).innerWidth() + settings.offset;
    grid.setGridWidth(width);
  }
}

$.fn.extend({ fluidGrid : jQuery.jgrid.fluid.fluidGrid });
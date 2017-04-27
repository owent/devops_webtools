(function (window, $) {
    var datetime_jqplot_options = {
        animate: true,
        animateReplot: true,
        seriesDefaults: {
            showMarker: true
        },
        cursor: {
            show: true,
            zoom: true,
            looseZoom: true,
            showTooltip: false
        },
        legend: {
            show: true
        },
        axes:{
            xaxis:{
                renderer:$.jqplot.DateAxisRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer
            }
        },
        highlighter: {
            show: true,
            showLabel: true,
            tooltipAxes: 'both',
            sizeAdjust: 12 ,
            tooltipLocation : 'ne'
        },
        series:[]
    };

    window.oss_config.group_by = {};
    window.oss_config.group_by.day = {
        'jqplot_options': jQuery.extend(true, {
                axes:{
                    xaxis:{
                        tickOptions: {
                            angle: -30,
                            formatString: '%F'
                        }
                    }
                }
            }, datetime_jqplot_options),
        'group': function (node, data, cache, node_id, seg) {
            var segs = oss_split_seg_names(seg);
            if (segs.length <= 1) {
                return;
            }

            var id = segs.shift();
            var id_cache = oss_make_cache(cache, node_id);

            var data_with_id = data[node_id];
            if (id != node_id) {
                for(var i = 0; i < data_with_id.length; ++ i) {
                    id_cache.raw.push(data_with_id[i]);
                }
            } else {
                for (var i = 0; i < data_with_id.length; ++i) {
                    var d = oss_pick_table(data_with_id[i], segs);
                    if (d !== null && d !== undefined) {
                        var dt = moment.unix(d);
                        var x_key = dt.format("YYYY-MM-DD");
                        id_cache.group[x_key] = id_cache.group[x_key] || [];
                        id_cache.group[x_key].push(data_with_id[i]);
                    }
                }
            }
        },
        'limit': function (node, x_node, cache, params, chart_options) {
            var limit_var = node.attr('limit');

            var min_bound = parseInt(x_node.attr('unit') || "1") * parseInt(x_node.attr('count') || "30");
            if (limit_var) {
                params["oss_x"] = limit_var;
                if (x_node.attr('count')) {
                    var unix_time = Math.floor(Date.now() / 1000);
                    params["oss_gt"] = moment().subtract(min_bound, "day").subtract(1, "second").unix();
                    params["oss_lt"] = moment().add(1, "second").unix();
                }
            }

            if (chart_options) {
                var unit = parseInt(x_node.attr('unit') || "1");
                var count = parseInt(x_node.attr('count') || "30");
                jQuery.extend(true, chart_options, {
                    axes: {
                        xaxis: {
                            min: moment().subtract(unit * count + unit, "day").format("YYYY-MM-DD"),
                            max: moment().add(unit, "day").format("YYYY-MM-DD")
                        }
                    }
                });
            }
        },

        'on_draw_chart': function(sender) {
            var min_day = null;
            var max_day = null;
            for(var yd in sender.chart_data) {
                for(var dd in sender.chart_data[yd]) {
                    var dg = sender.chart_data[yd][dd];
                    if(!min_day || min_day > dg[0]) {
                        min_day = dg[0];
                    }
                    if(!max_day || max_day < dg[0]) {
                        max_day = dg[0];
                    }
                }
            }

            $.extend(true, sender.chart_options, {
                axes: {
                    xaxis: {
                        min: min_day? moment(min_day).subtract(1, "day").format("YYYY-MM-DD"): undefined,
                        max: max_day? moment(max_day).add(1, "day").format("YYYY-MM-DD"): undefined
                    }
                }
            });
        }
    };

    window.oss_config.group_by.minute = {
        'jqplot_options': jQuery.extend(true, {
            axes: {
                xaxis: {
                    tickOptions: {
                        angle: -30,
                        formatString: '%m-%d %H:%M'
                    }
                }
            }
        }, datetime_jqplot_options),
        'group': function (node, data, cache, node_id, seg) {
            var segs = oss_split_seg_names(seg);
            if (segs.length <= 1) {
                return;
            }

            var id = segs.shift();
            var id_cache = oss_make_cache(cache, node_id);

            var data_with_id = data[node_id];
            if (id != node_id) {
                for (var i = 0; i < data_with_id.length; ++i) {
                    id_cache.raw.push(data_with_id[i]);
                }
            } else {
                for (var i = 0; i < data_with_id.length; ++i) {
                    var d = oss_pick_table(data_with_id[i], segs);
                    if (d !== null && d !== undefined) {
                        var dt = moment.unix(d);
                        var x_key = dt.format("YYYY-MM-DD HH:mm:ss");
                        id_cache.group[x_key] = id_cache.group[x_key] || [];
                        id_cache.group[x_key].push(data_with_id[i]);
                    }
                }
            }
        },
        'limit': function (node, x_node, cache, params, chart_options) {
            var limit_var = node.attr('limit');

            var min_bound = parseInt(x_node.attr('unit') || "1") * parseInt(x_node.attr('count') || "2880");
            if (limit_var) {
                params["oss_x"] = limit_var;
                if (x_node.attr('count')) {
                    var unix_time = Math.floor(Date.now() / 1000);
                    params["oss_gt"] = moment().subtract(min_bound, "minute").subtract(1, "second").unix();
                    params["oss_lt"] = moment().add(1, "second").unix();
                }
            }

            if (chart_options) {
                var unit = parseInt(x_node.attr('unit') || "1");
                var count = parseInt(x_node.attr('count') || "2880");
                jQuery.extend(true, chart_options, {
                    axes: {
                        xaxis: {
                            min: moment().subtract(unit * count + 15, "minute").format("YYYY-MM-DD HH:mm:ss"),
                            max: moment().add(15, "minute").format("YYYY-MM-DD HH:mm:ss")
                        }
                    }
                });
            }
        },

        'on_draw_chart': function (sender) {
            var min_val = null;
            var max_val = null;
            for (var yd in sender.chart_data) {
                for (var dd in sender.chart_data[yd]) {
                    var dg = sender.chart_data[yd][dd];
                    if (!min_val || min_val > dg[0]) {
                        min_val = dg[0];
                    }
                    if (!max_val || max_val < dg[0]) {
                        max_val = dg[0];
                    }
                }
            }

            $.extend(true, sender.chart_options, {
                axes: {
                    xaxis: {
                        min: min_val ? moment(min_val).subtract(15, "minute").format("YYYY-MM-DD HH:mm:ss") : undefined,
                        max: max_val ? moment(max_val).add(15, "minute").format("YYYY-MM-DD HH:mm:ss") : undefined
                    }
                }
            });
        }
    };
}) (window, jQuery);
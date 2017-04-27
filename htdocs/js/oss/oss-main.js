
/**
 * cache struct
 * cache.[id].raw = []
 * cache.[id].group = {
 *     x = [...]
 * }
 */

function oss_split_seg_names(segs) {
    if ('string' == typeof(segs)) {
        segs = segs.split('.');
        for (var i = 0; i < segs.length; ++i) {
            segs[i] = jQuery.trim(segs[i]);
        }
    }

    return segs;
}


function oss_pick_table(tb, segs) {
    segs = oss_split_seg_names(segs);

    var ret = tb;
    for(var i = 0; ret && i < segs.length; ++ i) {
        ret = ret[segs[i]];
    }

    return ret;
}

function oss_make_cache(cache, id) {
    if (!cache[id]) {
        cache[id] = {
            raw: [],
            group: {}
        }
    }

    return cache[id];
}

(function (window, $) {
    window.oss_config = window.oss_config || {};
}) (window, jQuery);

function oss_pick_cache(cache, x, seg, filter_fns) {
    var ret = [];

    var seg_names = oss_split_seg_names(seg);
    if (seg_names.length <= 1) {
        ret.push(oss_pick_table(cache, seg_names));
        if (!ret[0]) {
            ret.shift();
        }
        return ret;
    }

    var id = seg_names.shift();

    var x_cur = oss_pick_table(cache, [id, 'group', x]);
    var raw_cur = oss_pick_table(cache, [id, 'raw']);

    if ('function' == typeof(filter_fns)) {
        filter_fns = [filter_fns];
    }

    // 带分组的数据
    if (x_cur) {
        for(var i = 0; i < x_cur.length; ++ i) {
            // 过滤器
            var selected = true;
            if (filter_fns && filter_fns.length > 0) {
                for (var j = 0; j < filter_fns.length; ++ j) {
                    if (filter_fns[j] && false === filter_fns[j](x_cur[i])) {
                        selected = false;
                        break;
                    }
                }
            }
            if (selected) {
                var res = oss_pick_table(x_cur[i], seg_names);
                if (res !== null && res !== undefined) {
                    ret.push(res);
                }
            }
        }
    }

    // 不带分组的数据
    if (raw_cur) {
        for(var i = 0; i < raw_cur.length; ++ i) {
            // 过滤器
            var selected = true;
            if (filter_fns && filter_fns.length > 0) {
                for (var j = 0; j < filter_fns.length; ++ j) {
                    if (filter_fns[j] && false === filter_fns[j](x_cur[i])) {
                        selected = false;
                        break;
                    }
                }
            }
            if (selected) {
                var res = oss_pick_table(raw_cur[i], seg_names);
                if (res !== null && res !== undefined) {
                    ret.push(res);
                }
            }
        }
    }

    return ret;
}

function oss_build_function(code) {
    // 允许使用的内置函数
    // 筛选函数
    var filter = function(rule_text) {
        rule_text = rule_text
                        .replace(/\&gt;/g, '>')
                        .replace(/\&lt;/g, '<');
        var cmp_params = rule_text.match(/\s*([^\>\=\<]+)([\>\=\<]\=?)([^\s]+)\s*/);
        if (!cmp_params) {
            console.log('filter rule: ' + rule_text + ' invalid');
            return null;
        }

        var pick_data_fn = (function(s){
            var mres = s.match(/^\d*(\.?\d*)$/);
            if (mres) {
                if (mres[1]) {
                    var res = parseFloat(s);
                    return (function(){ return res; });
                } else {
                    var res = parseInt(s);
                    return (function(){ return res; });
                }
            } else {
                var segs = oss_split_seg_names(s);
                return (function(tb) {
                    return oss_pick_table(tb, segs) || 0;
                });
            }
        });

        var left = pick_data_fn(cmp_params[1]);
        var right = pick_data_fn(cmp_params[3]);

        if ('==' == cmp_params[2] || '=' == cmp_params[2]) {
            return (function(tb) { return left(tb) == right(tb); });
        } else if ('<' == cmp_params[2]) {
            return (function(tb) { return left(tb) < right(tb); });
        } else if ('<=' == cmp_params[2]) {
            return (function(tb) { return left(tb) <= right(tb); });
        } else if ('>' == cmp_params[2]) {
            return (function(tb) { return left(tb) > right(tb); });
        } else if ('>=' == cmp_params[2]) {
            return (function(tb) { return left(tb) >= right(tb); });
        } else {
            console.log('filter rule: ' + rule_text + ' invalid');
            return null;
        }
    }

    // 聚合函数
    var sum = function(seg, filter_rules){
        var cache = arguments.callee.caller.arguments[2];
        var x = arguments.callee.caller.arguments[3];

        var all_data = oss_pick_cache(cache, x, seg, filter_rules || null);
        var ret = 0;
        for (var k in all_data) {
            ret += all_data[k];
        }

        return ret;
    };

    var first = function(seg, filter_rules){
        var cache = arguments.callee.caller.arguments[2];
        var x = arguments.callee.caller.arguments[3];

        var all_data = oss_pick_cache(cache, x, seg, filter_rules || null);
        return all_data.length > 0? all_data[0]: 0;
    };

    var last = function(seg, filter_rules){
        var cache = arguments.callee.caller.arguments[2];
        var x = arguments.callee.caller.arguments[3];

        var all_data = oss_pick_cache(cache, x, seg, filter_rules || null);
        return all_data.length > 0? all_data[all_data.length - 1]: 0;
    };

    var count = function(seg, filter_rules) {
        var cache = arguments.callee.caller.arguments[2];
        var x = arguments.callee.caller.arguments[3];

        var all_data = oss_pick_cache(cache, x, seg, filter_rules || null);
        return all_data.length;
    };

    var val = sum;
    var avg = function(seg, filter_rules) {
        var cache = arguments.callee.caller.arguments[2];
        var x = arguments.callee.caller.arguments[3];

        var all_data = oss_pick_cache(cache, x, seg, filter_rules || null);
        var ret = 0;
        for (var k in all_data) {
            ret += all_data[k];
        }

        return ret / all_data.length;
    };

    var multi = function(){
        var cache = arguments.callee.caller.arguments[2];
        var x = arguments.callee.caller.arguments[3];

        var ret = 0;
        var all_datas = [];
        for (var i = 0; i < arguments.length; ++ i) {
            var seg = arguments[i];
            all_datas.push(oss_pick_cache(cache, x, seg));
        }

        if (0 == all_datas.length) {
            return 0;
        }

        for (var i = 0; i < all_datas[0].length; ++ i) {
            var res = 0;
            for (var j = 0; j < all_datas.length; ++j) {
                if (i < all_datas[j].length) {
                    if (0 == j) {
                        res = all_datas[j][i];
                    } else {
                        res *= all_datas[j][i];
                    }
                }
            }
            ret += res;
        }

        return ret;
    };

    try {
        eval("var ret = function (node,data,cache,x, cfg) { return " + code + "; }");
        return ret;
    } catch (e) {
        seed_alert(code + "<br />不是一个有效的公式脚本", e.toString());
        return null;
    }
}

function oss_load_configure() {
    var container_dom = jQuery("#sidebar-nav");
    container_dom.empty().html('<li>正在加载配置...</li>');
    jQuery.ajax({
        url: oss_config.api_url,
        data: {
            oss: 'api',
            oss_action: 'config'
        },
        cache: false,
        success: function(data) {
            var xml_data = $(data);
            if($('file_not_found', xml_data).html()) {
                seed_alert($('file_not_found', xml_data).html() + " not found!", "configure file not found");
                return;
            }

            oss_config.instance = {
                config: $("cat", xml_data),
                event_dispatcher: jQuery({})
            }
            container_dom.empty();

            var is_not_first = false;
            jQuery.each(oss_config.instance.config, function(k, v){
                var cat_jxml = jQuery(v);
                var cat_dom = $("<li></li>").append($("<a></a>").attr({ href: 'javascript:void(0);' }).html(cat_jxml.attr('name')));
                var cat_list = $("<ul></ul>").attr({ 'class': 'nav nav-pills nav-stacked drop', style: 'display: block;' });
                cat_dom.append(cat_list);

                jQuery.each(jQuery(">view", v), function(vk, vv) {
                    var vv_jxml = jQuery(vv);
                    var item_li = $('<li></li>');
                    var item_a = $('<a></a>').attr({
                        'href': 'javascript: void(0);',
                        'title': vv_jxml.attr('name')
                    });

                    var icon = vv_jxml.attr('icon') || 'eye-open';
                    item_a.append('<span class="glyphicon glyphicon-' + icon + '"></span>&nbsp;' + (vv_jxml.attr('name') || 'No Name') );
                    (function(view, jdom){
                        jdom.click(function(){ oss_load_chart(view); });
                    })(vv_jxml, item_a);

                    item_li.append(item_a);
                    cat_list.append(item_li);
                });

                (function(evt_dom, opr_dom){
                    evt_dom.click(function(){ opr_dom.slideToggle(); });
                })(jQuery('>a', cat_dom), cat_list);

                container_dom.append(cat_dom);
                if (is_not_first) {
                    cat_list.hide();
                }
                is_not_first = true;
            });
        },
        dataType: 'xml',
        error: function(jqXHR, textStatus, errorThrown) {
            seed_alert(textStatus, "load configure file failed");
        }
    });
}


function oss_load_chart(view) {
    var all_ds = jQuery("data_source", view);
    var cache = {};
    var data = {};
    var x_node = jQuery("x", view);
    var method_group = x_node.attr("method");
    if (!oss_config.group_by[method_group]) {
        seed_alert("x.method in " + all_ds.attr('name') + " invalid");
        return;
    }
    method_group = oss_config.group_by[method_group];

    var subtitle = ' - ' + jQuery(view.parent().get(0)).attr("name") + ' - ' + view.attr('name');
    jQuery('#oss-subtitle').html(subtitle);
    jQuery('#oss-chart').attr("render-label", subtitle);
    jQuery('#oss-chart').html('正在加载数据...<img alt="loading..." href="img/waiting.gif" />');

    var chart_options = {
        //title: {
        //    text:view.attr('name'),
        //    show: false
        //},
        series: [],
        axes:{
            xaxis:{
                label: x_node.attr('name')
            }
        }
    };

    var action_done = function() {
        var chart_data_ys = [];

        if (jQuery('#oss-chart').attr("render-label") != subtitle) {
            return;
        }

        // 构建数据和y轴数据
        var x_id = oss_split_seg_names(x_node.html())[0];
        var all_y_conf = jQuery("y", view);
        for(var i = 0; i < all_y_conf.length; ++ i) {
            var y_conf = jQuery(all_y_conf.get(i));
            var y_val_fn = oss_build_function(y_conf.html());

            if (!y_val_fn) {
                continue;
            }

            var y_val_set = [];
            for(var kx in cache[x_id].group) {
                var res = y_val_fn(y_conf, data, cache, kx, {});
                if (res) {
                    y_val_set.push([kx, res]);
                }
            }

            if (y_val_set.length > 0) {
                chart_data_ys.push(y_val_set);
                chart_options.series.push({
                    label: y_conf.attr('name'),
                    yaxis: y_conf.attr('axis') || "yaxis"
                });
            }
        }

        if (method_group.jqplot_options) {
            jQuery.extend(true, chart_options, method_group.jqplot_options);
        }
        jQuery('#oss-chart').empty();

        if (method_group.on_draw_chart) {
            method_group.on_draw_chart({
                dom_id: 'oss-chart',
                chart_options: chart_options,
                chart_data: chart_data_ys,
                raw_data: data,
                raw_cache: cache,
                view: view
            });
        }

        if (chart_data_ys.length > 0) {
            jQuery.jqplot('oss-chart', chart_data_ys, chart_options);
        } else {
            jQuery('#oss-chart').html('配置错误或无数据');
        }
    };

    function load_data_source(loading_index) {
        if (loading_index >= all_ds.length) {
            action_done();
            return;
        }

        var node = jQuery(all_ds.get(loading_index));
        var params = {
            oss: 'api',
            oss_action: 'mongodb',
            oss_env: oss_config.env,
            oss_db: node.attr('db'),
            oss_table: node.attr('table')
        }

        if (node.attr('count')) {
            params["oss_count"] = node.attr('count');
        }

        if (method_group.limit) {
            method_group.limit(node, x_node, cache, params, chart_options);
        }

        jQuery.ajax({
            url: oss_config.api_url,
            data: params,
            cache: false,
            success: function(data_obj) {
                if (data_obj && 0 === data_obj.ret) {
                    var node_id = node.attr('id');
                    data[node_id] = data_obj.data || [];

                    if(method_group.group) {
                        method_group.group(node, data, cache, node_id, x_node.html());
                    }
                } else {
                    seed_alert(data_obj.msg || 'data.msg not found', "data content invalid");
                }

                load_data_source(loading_index + 1);
            },
            dataType: 'json',
            error: function(jqXHR, textStatus, errorThrown) {
                seed_alert(textStatus, "load data source " + node.attr("id") + " failed");
                load_data_source(loading_index + 1);
            }
        });
    }

    load_data_source(0);
}

jQuery(document.body).ready(function(){
    oss_load_configure();
});

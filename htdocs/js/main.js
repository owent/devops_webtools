/***
 * Util包
 * TQueryString类 版本1.7
 * Licensed under the MIT licenses.
 * 用于把查询参数转换为结构体的类
 *
 * <code>
 *     Util.TQueryString(initKey, initValue, option) // 获取QueryString信息类
 *
 *     Function:
 *     Util.TQueryString().getItem(key)              //
 * 获取QueryString信息类中某个属性值
 *     Util.TQueryString().getKeys()                 //
 * 获取QueryString信息类中的所有属性名
 *     Util.TQueryString().setItem(key, value)       // 设置属性
 *     Util.TQueryString().removeItem(key)           // 删除属性
 *     Util.TQueryString().clear()                   // 删除所有属性
 *     Util.TQueryString().toString()                // 转换为URL字符串
 *     Util.TQueryString().getFromUrl(uri, split)    //
 * 将自定义或当前URL的参数加入到TQueryString信息类中
 *
 *     Member:
 *     m_jQueryString                          //
 * 记录当前配置项信息的结构，JSON语法
 * </code>
 *
 * @Author OWenT
 * @Version 1.6
 * @Link   http://www.owent.net
 *
 * @Class TQueryString {
 *     @Function {
 *         TQueryString(initKey, initValue, opt)
 *         @Param  {
 *             initKey: 初始关键字名，也可以用JSON赋值,
 *             initValue: 初始关键字内容，initKey用JSON赋值时此参数无效
 *             opt: 覆盖的配置项(JSON格式)
 *         }
 *         @return TQueryString类
 *
 *         getItem(key)
 *         @Param  {
 *             key: 属性名称或索引
 *         }
 *         @return 属性值
 *
 *         getKeys()
 *         @return 属性名列表
 *
 *         setItem(key, value)
 *         @Param  {
 *             key: 属性名称或JSON或参数字符串,
 *             value：属性值(当key为JSON或url时参数无效)
 *         }
 *         @return TQueryString类
 *
 *         removeItem(key)
 *         @Param  {
 *             key: 属性名称、索引下标或属性数组或JSON（为JSON时仅关键字有效）
 *         }
 *         @return TQueryString类
 *
 *         clear()
 *         @return TQueryString类
 *
 *         toString()
 *         @return 转化成URL形式的QueryString的字符串
 *
 *         getFromUrl(uri, split)
 *         @Param  {
 *             uri: 需要提取参数的URL（可选，默认为当前URL）
 *             split: 参数分隔符（正则表达式，可选，默认为 /[\?#]/gim）
 *         }
 *         @return TQueryString类
 *     }
 * }
 *
 * Example
 * <code>
 *     //左边为代码示例，右边是改变的结果
 *     var qs = Util.TQueryString();           // {}
 *     qs.setItem("para1=1&para2=2");          // {para1: '1', para2: '2'}
 *     qs.setItem("para3", "3");               // {para1: '1', para2: '2',
 * para3: '3'}
 *     qs.setItem({para4: 4});                 // {para1: '1', para2: '2',
 * para3: '3', para4: 4}
 *     qs.getItem("para2")                     // '2'
 *     qs.getItem(3)                           // '4'
 *     qs.getKeys()                            // ["para1", "para2", "para3",
 * "para4"]
 *     qs.removeItem("para2")                  // {para1: '1', para3: '3',
 * para4: 4}
 *     qs.removeItem(0)                        // {para3: '3', para4: '4'}
 *     qs.toString()                           // "para3=3&para4=4"
 *     qs.removeItem(['para3', 'para4'])       // {para3: '3', para4: '4'}
 *     qs.clear()                              // {}
 *
 *     // 以下为复杂应用
 *     var qs = Util.TQueryString({para3:[1,2,3,"4a"]});     // 初始化构造
 *     qs.setItem({para4:{a4:'va4', b4: 'vb4'}});            // 设置复杂项
 *     qs.setItem({para5:{a5:['ar5', 'arr5'], b5: {bj5: 'bjv5'}}});
 *     qs.setItem("para6", ['ar6-begin', {arj61: 'arjv61', arj62: 'arjv62'},
 * ['arr61', 'arr62', 'arr63'], 'ar6-end']);
 *     qs.toString();
 *     // <output>
 *     //
 * "para3%5B%5D=1&para3%5B%5D=2&para3%5B%5D=3&para3%5B%5D=4a&para4%5Ba4%5D=va4&para4%5Bb4%5D=vb4&para5%5Ba5%5D%5B%5D=ar5&para5%5Ba5%5D%5B%5D=arr5&para5%5Bb5%5D%5Bbj5%5D=bjv5&para6%5B%5D=ar6-begin&para6%5B1%5D%5Barj61%5D=arjv61&para6%5B1%5D%5Barj62%5D=arjv62&para6%5B2%5D%5B%5D=arr61&para6%5B2%5D%5B%5D=arr62&para6%5B2%5D%5B%5D=arr63&para6%5B%5D=ar6-end"
 *     // </output>
 *  </code>
 */
window.Util = window.Util || {};
window.Util.TQueryString = (function(initKey, initValue, opt) {
  var config = ({
    keys_filter: /(\[[^\[\]]*\])|([^\[\]]+)/gim,
    key_filter: /([^\[\]]+)/gim,
    split_filter: /[\?#&\$]/gim
  });

  // 配置覆盖
  opt = opt || ({});
  for (var key_opt in opt) {
    config[key_opt] = opt[key_opt];
  }

  var getType = (function(obj) {
    if (obj === null) return String(obj);
    var res = (typeof(obj)).toLowerCase(), ret;
    if (res == "object" &&
        (ret = Object.prototype.toString.call(obj).toLowerCase().match(
             /object (\w*)/i)) &&
        ret.length > 1)
      return ret[1];
    return res;
  });
  var j2s = (function(key, value, pre) {
    pre = pre || "";
    return parseString(value, pre + encodeURIComponent("[" + key + "]"));
  });
  var a2s = (function(key, value, pre) {
    pre = pre || "";
    if (getType(value) == "array" || getType(value) == "object") {
      return parseString(value, pre + encodeURIComponent("[" + key + "]"));
    } else {
      return parseString(value, pre + encodeURIComponent("[]"));
    }
  });
  var a2j = (function(src) {
    var ret = ({});
    for (var key_a2j in src) {
      ret[key_a2j] = src[key_a2j];
    }
    return ret;
  });

  var s2aj = (function(base_val, index, key_list, val) {
    // 确定值
    if (index >= key_list.length) {
      // 空项
      if (val === null || val === undefined) return val;
      // 数字类型转换,不接受NaN
      if (val && !isNaN(val)) return Number(val);
      // 布尔类型转换
      if (val == "true" || val == "false") return Boolean(val == "true");
      // 字符串转码
      return String(val.toString());
    }

    var key_name = key_list[index].match(config.key_filter);
    key_name = (key_name && key_name.length > 0) ? key_name[0] : null;
    base_val = base_val || ([]);
    if (key_name && key_name.length > 0) {
      // json结构
      if (getType(base_val) == "array" &&
          parseInt(key_name).toString() != key_name)
        base_val = a2j(base_val);
      base_val[key_name] =
          s2aj(base_val[key_name] || null, index + 1, key_list, val);
    } else {
      // 数组结构
      base_val.push(s2aj(null, index + 1, key_list, val));
    }
    return base_val;
  });

  var parseString = (function(value, pre) {
    var ret = "";
    pre = pre || "";
    if (value === null || value === undefined) {
      return ret;
    } else if (getType(value) == "array") {
      for (var key in value) {
        ret += a2s(key, value[key], pre);
      }
    } else if (getType(value) == "object") {
      for (var key in value) {
        ret += j2s(key, value[key], pre);
      }
    } else {
      ret = pre + "=" + encodeURIComponent(value.toString()) + "&";
    }
    return ret;
  });

  var jsonQS = ({
    m_jQueryString: {},
    getItem: (function(key) {
      if (getType(key) == "number")
        return this.m_jQueryString[this.getKeys()[key]];
      if (getType(key) == "string") return this.m_jQueryString[key];
      return null;
    }),
    getKeys: (function() {
      var ret = [];
      for (var key in this.m_jQueryString) ret.push(key);
      return ret;
    }),
    setItem: (function(key, value) {
      if (value || getType(key) == "string") {
        if (value === null) {
          this.m_jQueryString[key] = value;
        } else if (value != null) {
          this.m_jQueryString[key] = value;
        } else {
          if (key.length == 0) return this;
          var kg = key.split(config.split_filter);
          for (var i in kg) {
            var p = kg[i].lastIndexOf('=');
            var key_list = [], val = "";
            if (p > 0 && p < kg[i].length) {
              key_list = decodeURIComponent(kg[i].substr(0, p))
                             .match(config.keys_filter);
              val =
                  decodeURIComponent(kg[i].substr(p + 1, kg[i].length - p - 1));
            } else if (kg[i]) {
              key_list = decodeURIComponent(kg[i]).match(config.keys_filter);
            }
            if (key_list.length > 0) {
              var key_name = key_list[0];
              this.setItem(
                  key_name,
                  s2aj(
                      this.m_jQueryString[key_name] || null, 1, key_list, val));
            }
          }
        }
      } else if (getType(key) == "object") {
        for (var ky in key) {
          this.setItem(ky, key[ky]);
        }
      }
      return this;
    }),
    removeItem: (function(key) {
      if (getType(key) == "number")
        delete this.m_jQueryString[this.getKeys()[key]];
      else if (getType(key) == "string")
        delete this.m_jQueryString[key];
      else if (getType(key) == "object") {
        for (var i in key) {
          this.removeItem(key[i]);
        }
      } else if (getType(key) == "array") {
        for (var i in key) {
          this.removeItem(key[i]);
        }
      }
      return this;
    }),
    clear: (function() {
      for (var key in this.m_jQueryString) this.removeItem(key);
      return this;
    }),
    toString: (function() {
      var ret = "";
      for (var key_ts in this.m_jQueryString) {
        ret += parseString(
            this.m_jQueryString[key_ts], encodeURIComponent(key_ts));
      }
      return ret.substr(0, ret.length - 1);
    }),
    getFromUrl: (function(uri, spl_sym) {
      uri = uri || document.URL;
      spl_sym = spl_sym || config.split_filter;
      var params = uri.split(spl_sym);
      for (var i_url in params) {
        if (params[i_url].lastIndexOf("=") >= 0) this.setItem(params[i_url]);
      }

      return this;
    })
  });
  if (initKey) jsonQS.setItem(initKey, initValue);
  return jsonQS;
});

function webtools_login(channel_name) {
  if (!channel_name) {
    for (var key in webtools_conf) {
      channel_name = key;
      break;
    }
  }

  if (!channel_name) {
    seed_alert("暂未接入账号授权系统！");
    return;
  }

  var channel_oauth = webtools_conf[channel_name] || {};
  if (channel_oauth && channel_oauth.oauth_login) {
    document.cookie = "oauth_redirect=" + encodeURIComponent(location.href);
    document.cookie = "oauth_type=" + encodeURIComponent(channel_name);
    location.href = channel_oauth.oauth_login;
  } else {
    seed_alert("暂未接入账号授权系统！");
  }
}

$(document)
    .ready(function() {
      for (var channel_name in webtools_conf) {
        var login_btn = $("<a></a>");
        login_btn.attr({
          'href': 'javascript:' +
              'webtools_login("' + channel_name + '");'
        });
        login_btn.html("login with " + channel_name);

        $('#login').append(" | ").append(login_btn);
      }

      $('#show_link')
          .click(function() {
            $("ul.drop").slideToggle('normal');
            return false;
          });
    });


(function($) {
  $.metro = (function() {
    var _push_alert_wrapper = null;
    var _body = null;

    var _get_body = (function() {
      if (_body) return _body;
      return _body = $(document.body);
    });

    this.getAlertWrapper = (function() {
      if (_push_alert_wrapper) return _push_alert_wrapper;

      _push_alert_wrapper = $("<div></div>");
      _push_alert_wrapper.addClass("push_alert_wrapper");
      _get_body().append(_push_alert_wrapper);

      return _push_alert_wrapper;
    });

    return this;
  })();

  $.fn.extend({
    pushMessage: function(opt) {
      var jt = $("<div></div>"), jc = $("<div></div>"), je = this;
      opt = opt || {};
      opt = $.extend({"time": 500, "standTime": 5000, "slideUpTime": 500}, opt);

      jc.css("clear", "both");
      jt.addClass("push_alert_element").append(je);
      $.metro.getAlertWrapper().append(jt).append(jc);

      jt.addClass("sys_panel").css("opacity", 0.0);
      jt.animate({"right": "+=100%", "opacity": 1.0}, opt["time"], function() {
        var killTime = parseInt(opt["standTime"]);
        var killTimerFunc = (function() {
          if (killTime <= 0) {
            jt.animate(
                {"right": "-=100%", "opacity": 0.0}, opt["time"], function() {
                  jt.remove();
                  jc.remove();
                });
            return;
          }
          killTime -= 200;
          setTimeout(function() { killTimerFunc(); }, 200);
        });
        jt.hover(
            function() { killTime = 1000 * 3600 * 24 * 7; },
            function() { killTime = parseInt(opt["standTime"]); });
        killTimerFunc();
      });
      return this;
    }
  });
})(jQuery);

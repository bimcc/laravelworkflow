/*common 公共JS*/

/**
 * 弹出层
 * @param title 层标题
 * @param url 层链接(opt.type=2|默认)或者HTML内容(opt.type=1)
 * @param opt 选项 {w:WIDTH('800px|80%'),h:HEIGHT('600px|80%'),type:1|2,fn:CALLBACK(回调函数),confirm:BOOL(关闭弹层警告)}
 */
function layer_open(title, url, opt) {
    if (typeof opt === "undefined") opt = {nav: true};
    w = opt.w || "80vw";
    h = opt.h || "80vh";
    // 不支持vh,vw单位时采取js动态获取
    if (!attr_support('height', '10vh')) {
        w = w.replace(/([\d\.]+)(vh|vw)/, function (source, num, unit) {
            return $(window).width() * num / 100 + 'px';
        });
        h = h.replace(/([\d\.]+)(vh|vw)/, function (source, num, unit) {
            return $(window).height() * num / 100 + 'px';
        });
    }
    return layer.open({
        type: opt.type || 2,
        area: [w, h],
        fix: false, // 不固定
        maxmin: true,
        shade: 0.4,
        title: title,
        content: url,
        success: function (layero, index) {
            if (typeof opt.confirm !== "undefined" && opt.confirm === true) {
                layero.find(".layui-layer-close").off("click").on("click", function () {
                    layer.alert('您确定要关闭当前窗口吗？', {
                        btn: ['确定', '取消'] //按钮
                    }, function (i) {
                        layer.close(i);
                        layer.close(index);
                    });
                });
            }
            // 自动添加面包屑导航
            if (true === opt.nav) {
                layer.getChildFrame('#nav-title', index).html($('#nav-title').html() + ' <span class="c-gray en">&gt;</span> ' + $('.layui-layer-title').html());
            }
            if (typeof opt.fn === "function") {
                opt.fn(layero, index);
            }
        }
    });
};

/**
 * 全屏打开窗口，参数见layer_open
 */
function full_page(title, url, opt) {
    return layer_open(title, url, $.extend({w: "100%", h: "100%"}, opt))
};

/**
 * iframe内打开新窗口
 * @param title
 * @param url
 */
function open_window(title, url) {
    //解决在非iframe页里打开不了页面的问题
    if (window.parent.frames.length == 0) {
        window.open(url);
        return false;
    }
    var bStop = false;
    var bStopIndex = 0;
    var topWindow = $(window.top.parent.document);
    var show_navLi = topWindow.find("#min_title_list li");
    var iframe_box = topWindow.find('#iframe_box');
    show_navLi.each(function () {
        if ($(this).find('span').attr("data-href") == url) {
            bStop = true;
            bStopIndex = show_navLi.index($(this));
            return false;
        }
    });
    if (!bStop) {
        var show_nav = topWindow.find('#min_title_list');
        show_nav.find('li').removeClass("active");
        show_nav.append('<li class="active"><span data-href="' + url + '">' + title + '</span><i></i><em></em></li>');
        var taballwidth = 0,
            $tabNav = $(".acrossTab", window.top.parent.document),
            $tabNavitem = $(".acrossTab li", window.top.parent.document);
        $tabNavitem.each(function (index, element) {
            taballwidth += Number(parseFloat($(this).width() + 60))
        });
        $tabNav.width(taballwidth + 25);
        var iframeBox = iframe_box.find('.show_iframe');
        iframeBox.hide();
        iframe_box.append('<div class="show_iframe"><div class="loading"></div><iframe frameborder="0" src=' + url + '></iframe></div>');
        var showBox = iframe_box.find('.show_iframe:visible');
        showBox.find('iframe').attr("src", url).load(function () {
            showBox.find('.loading').hide();
        });
    }
    else {
        show_navLi.removeClass("active").eq(bStopIndex).addClass("active");
        iframe_box.find(".show_iframe").hide().eq(bStopIndex).show().find("iframe").attr("src", url);
    }

}

/**
 * ajax 处理，对应服务端 ajax_return_adv 方法返回的 json 数据处理
 * @param data ajax返回数据
 * @param callback 成功回调函数
 * @param param 回调参数
 */
function ajax_progress(data, callback, param) {
    if (data.code == 0) {
		layer.msg(data.msg,{icon:1,time: 1500},function(){
                parent.location.reload(); // 父页面刷新
        });          
    } else {
       layer.alert(data.msg, {title: "错误信息", icon: 2});
    }
}
/**
 * 表格无限宽横向溢出
 * @param selector
 * @param width 不赋值默认为th的width值和
 * @param force 强制将表格宽度设置成实际的宽度
 */
function table_fixed(selector, width, force) {
    var attr = typeof force == 'undefined' ? 'min-width' : 'width';
    $(selector).each(function () {
        $this = $(this);
        //未设置宽度自动获取width属性的宽
        if (typeof width === "undefined") {
            width = 0;
            $this.find("tr:first th").each(function () {
                width += parseInt($(this).attr("width") || $(this).innerWidth());
            })
        }
        $this.css(attr, width + "px");
        $this.css("table-layout", "fixed");
        $this.wrap('<div style="width:100%;overflow:auto"></div>');
    });
}

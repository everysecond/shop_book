var config = {
    name: 'CPS租点动力数据全景',
    width: 3840,
    height: 2160,
    scale: 1,
    backgroundImage: './img/bg/bg1.png',
    url: 'http://yapi.demo.qunar.com/mock/9013',
    mark: {},
    gradeShow: false,
    gradeLen: 30,
    query: {}
}
var list = [
    {
        "title": "文本框",
        "name": "CPS中台业务数据全景",
        "icon": "icon-text",
        "data": "CPS中台业务数据全景",
        "component": {
            "width": 3836.73,
            "height": 116.48,
            "option": {
                "textAlign": "center",
                "fontSize": 80,
                "fontWeight": "bolder",
                "color": "rgba(30, 144, 255, 1)",
                "split": 10
            },
            "name": "text",
            "prop": "text"
        },
        "left": 0,
        "top": 8.31,
        "zIndex": 0,
        "index": 0
    },
    {
        "title": "图片框",
        "name": "图片框-租点-业务概览",
        "icon": "icon-img",
        "top": 150,
        "left": 10,
        "component": {
            "width": 940,
            "height": 560,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 1,
        "index": 1,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-租点-业务概览",
        "icon": "icon-text",
        "data": "租点-业务概览",
        "component": {
            "width": 213.57,
            "height": 33.38,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 50,
        "top": 200,
        "zIndex": 2,
        "index": 2
    },
    {
        "title": "图片框",
        "name": "图片框-租赁趋势",
        "icon": "icon-img",
        "top": 730,
        "left": 10,
        "component": {
            "width": 940,
            "height": 700,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 3,
        "index": 3,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-租赁趋势",
        "icon": "icon-text",
        "data": "租赁趋势",
        "component": {
            "width": 141.55,
            "height": 50,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 50,
        "top": 780,
        "zIndex": 4,
        "index": 4
    },
    {
        "title": "图片框",
        "name": "图片框-业务统计",
        "icon": "icon-img",
        "top": 1450,
        "left": 10,
        "component": {
            "width": 940,
            "height": 700,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 5,
        "index": 5,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-业务统计",
        "icon": "icon-text",
        "data": "业务统计",
        "component": {
            "width": 141.55,
            "height": 50,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 50,
        "top": 1495,
        "zIndex": 6,
        "index": 6
    },
    {
        "title": "图片框",
        "name": "图片框-租点-实时数据",
        "icon": "icon-img",
        "top": 150,
        "left": 975.54,
        "component": {
            "width": 940,
            "height": 560,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 7,
        "index": 7,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-租点-实时数据",
        "icon": "icon-text",
        "data": "租点-实时数据",
        "component": {
            "width": 213.57,
            "height": 33.38,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1010,
        "top": 200,
        "zIndex": 8,
        "index": 8
    },
    {
        "title": "图片框",
        "name": "图片框-区域分布",
        "icon": "icon-img",
        "top": 730,
        "left": 970,
        "component": {
            "width": 940,
            "height": 700,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 9,
        "index": 9,
        "data": "./img/border/border1.png"
    },
    {
        "title": "滑动组件",
        "name": "滑动组件-区域分布",
        "icon": "icon-img",
        "top": 810,
        "left": 970,
        "child": {
            "index": [
                26,
                27
            ]
        },
        "component": {
            "width": 940,
            "height": 591.36,
            "name": "slide",
            "prop": "slide",
            "option": {
                "autoplay": true,
                "delay": 5000,
                "title": "车主用户"
            }
        },
        "zIndex": 103,
        "index": 103,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-区域分布",
        "icon": "icon-text",
        "data": "区域分布",
        "component": {
            "width": 141.55,
            "height": 50,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1010,
        "top": 780,
        "zIndex": 10,
        "index": 10
    },
    {
        "title": "图片框",
        "name": "图片框-增长趋势",
        "icon": "icon-img",
        "top": 1450,
        "left": 970,
        "component": {
            "width": 940,
            "height": 700,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 11,
        "index": 11,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-增长趋势",
        "icon": "icon-text",
        "data": "增长趋势",
        "component": {
            "width": 141.55,
            "height": 50,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1010,
        "top": 1495,
        "zIndex": 12,
        "index": 12
    },
    {
        "title": "图片框",
        "name": "图片框-快点-业务概览",
        "icon": "icon-img",
        "top": 150,
        "left": 1930,
        "component": {
            "width": 940,
            "height": 560,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 13,
        "index": 13,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-快点-业务概览",
        "icon": "icon-text",
        "data": "快点-业务概览",
        "component": {
            "width": 213.57,
            "height": 33.38,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1970,
        "top": 200,
        "zIndex": 14,
        "index": 14
    },
    {
        "title": "图片框",
        "name": "图片框-业务趋势",
        "icon": "icon-img",
        "top": 730,
        "left": 1930,
        "component": {
            "width": 940,
            "height": 700,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 15,
        "index": 15,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-业务趋势",
        "icon": "icon-text",
        "data": "业务趋势",
        "component": {
            "width": 141.55,
            "height": 50,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1970,
        "top": 780,
        "zIndex": 16,
        "index": 16
    },
    {
        "title": "图片框",
        "name": "图片框-销售统计",
        "icon": "icon-img",
        "top": 1450,
        "left": 1930,
        "component": {
            "width": 940,
            "height": 700,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 17,
        "index": 17,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-销售统计",
        "icon": "icon-text",
        "data": "销售统计",
        "component": {
            "width": 141.55,
            "height": 50,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1970,
        "top": 1495,
        "zIndex": 18,
        "index": 18
    },
    {
        "title": "图片框",
        "name": "图片框-快点-实时数据",
        "icon": "icon-img",
        "top": 150,
        "left": 2890,
        "component": {
            "width": 940,
            "height": 560,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 13,
        "index": 13,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-快点-实时数据",
        "icon": "icon-text",
        "data": "快点-实时数据",
        "component": {
            "width": 213.57,
            "height": 33.38,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 2930,
        "top": 200,
        "zIndex": 14,
        "index": 14
    },
    {
        "title": "图片框",
        "name": "图片框-区域库存",
        "icon": "icon-img",
        "top": 730,
        "left": 2890,
        "component": {
            "width": 940,
            "height": 700,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 15,
        "index": 15,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-区域库存",
        "icon": "icon-text",
        "data": "区域库存",
        "component": {
            "width": 141.55,
            "height": 50,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 2930,
        "top": 780,
        "zIndex": 16,
        "index": 16
    },
    {
        "title": "图片框",
        "name": "图片框-回收统计",
        "icon": "icon-img",
        "top": 1450,
        "left": 2890,
        "component": {
            "width": 940,
            "height": 700,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 17,
        "index": 17,
        "data": "./img/border/border1.png"
    },
    {
        "title": "文本框",
        "name": "文本框-回收统计",
        "icon": "icon-text",
        "data": "回收统计",
        "component": {
            "width": 141.55,
            "height": 50,
            "option": {
                "textAlign": "center",
                "fontSize": 30,
                "fontWeight": "normal",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 2930,
        "top": 1495,
        "zIndex": 18,
        "index": 18
    },
    {
        "name": "柱状图-车主用户",
        "title": "柱状图-车主用户",
        "icon": "icon-bar",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/lease/data/userArea",
        "time": 30000,
        "component": {
            "width": 895.3,
            "height": 522.44,
            "name": "bar",
            "prop": "bar",
            "option": {
                "gridX": 105,
                "gridY": 85,
                "gridX2": 82,
                "gridY2": 99,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "xNameFontSize": 24,
                "yNameFontSize": 24,
                "labelShow": true,
                "labelShowFontSize": 24,
                "labelShowFontWeight": 300,
                "yAxisInverse": false,
                "xAxisInverse": true,
                "xAxisShow": true,
                "yAxisShow": true,
                "xAxisSplitLineShow": false,
                "yAxisSplitLineShow": false,
                "barWidth": 30,
                "barRadius": 8,
                "barColor": [
                    {
                        "color1": "#83bff6",
                        "color2": "#188df0",
                        "postion": 90,
                        "$index": 0,
                        "_show": true
                    },
                    {
                        "color1": "#23B7E5",
                        "color2": "#564AA3",
                        "postion": 50,
                        "$index": 1,
                        "_show": true
                    }
                ],
                "barMinHeight": 2,
                "category": false,
                "title": "车主用户",
                "titleFontSize": 30,
                "titlePostion": "center",
                "titleShow": true,
                "titleColor": "rgba(30, 144, 255, 1)",
                "xAxisRotate": false,
                "tipFontSize": 24,
                "tipColor": "#fff",
                "labelShowColor": "#fff"
            }
        },
        "left": 991.66,
        "top": 858.7,
        "zIndex": 26,
        "index": 26,
        "display": true
    },
    {
        "name": "柱状图-合作网点",
        "title": "柱状图-合作网点",
        "icon": "icon-bar",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/service/serviceArea",
        "time": 30000,
        "component": {
            "width": 895.3,
            "height": 522.44,
            "name": "bar",
            "prop": "bar",
            "option": {
                "gridX": 105,
                "gridY": 85,
                "gridX2": 82,
                "gridY2": 99,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "xNameFontSize": 24,
                "yNameFontSize": 24,
                "labelShow": true,
                "labelShowFontSize": 24,
                "labelShowFontWeight": 300,
                "yAxisInverse": false,
                "xAxisInverse": false,
                "xAxisShow": true,
                "yAxisShow": true,
                "xAxisSplitLineShow": false,
                "yAxisSplitLineShow": false,
                "barWidth": 30,
                "barRadius": 8,
                "barColor": [
                    {
                        "color1": "#83bff6",
                        "color2": "#188df0",
                        "postion": 90,
                        "$index": 0,
                        "_show": true
                    },
                    {
                        "color1": "#23B7E5",
                        "color2": "#564AA3",
                        "postion": 50,
                        "$index": 1,
                        "_show": true
                    }
                ],
                "barMinHeight": 2,
                "category": false,
                "title": "合作网点",
                "titleFontSize": 30,
                "titlePostion": "center",
                "titleShow": true,
                "titleColor": "rgba(30, 144, 255, 1)",
                "xAxisRotate": false,
                "tipFontSize": 24,
                "tipColor": "#fff",
                "labelShowColor": "#fff"
            }
        },
        "left": 991.66,
        "top": 858.7,
        "zIndex": 27,
        "index": 27,
        "display": true
    },
    {
        "title": "图片",
        "name": "图片",
        "icon": "icon-img",
        "top": 235.45,
        "left": 1002.74,
        "component": {
            "width": 140,
            "height": 140,
            "name": "img",
            "prop": "img",
            "option": {
                "duration": 5,
                "opacity": 1,
                "rotate": true
            }
        },
        "zIndex": 28,
        "index": 28,
        "data": "./img/source/source242.png"
    },
    {
        "title": "图片",
        "name": "图片",
        "icon": "icon-img",
        "top": 229.91,
        "left": 1468.1,
        "component": {
            "width": 140,
            "height": 140,
            "name": "img",
            "prop": "img",
            "option": {
                "duration": 5,
                "opacity": 1,
                "rotate": true
            }
        },
        "zIndex": 29,
        "index": 29,
        "data": "./img/source/source242.png"
    },
    {
        "title": "翻牌器-今日新增用户",
        "name": "翻牌器-今日新增用户",
        "icon": "icon-flop",
        "top": 268.69,
        "left": 1152.32,
        "dataType": 1,
        "data": {
            "value": "12345"
        },
        "component": {
            "width": 309.86,
            "height": 124.93,
            "name": "flop",
            "prop": "flop",
            "option": {
                "type": "",
                "suffixText": "",
                "suffixTextAlign": "right",
                "suffixSplit": "",
                "suffixColor": "",
                "suffixFontSize": 0,
                "borderColor": "#fff",
                "borderWidth": 3,
                "backgroundBorder": "./img/border/border1.png",
                "fontSize": 42,
                "fontWeight": "normal",
                "splitx": 0,
                "splity": 0,
                "backgroundColor": "",
                "color": "#fff",
                "prefixTextAlign": "left",
                "prefixFontSize": 30,
                "row": false,
                "whole": true,
                "prefixColor": "",
                "width": 250,
                "textAlign": "left",
                "prefixSplitx": 3,
                "prefixSplity": 4,
                "suffixSplitx": 6,
                "suffixSplity": 4
            }
        },
        "index": 30,
        "zIndex": 30,
        "url": "/manage/service/actualData/1",
        "time": 30000,
        "dataMethod": "get",
        "display": false
    },
    {
        "title": "翻牌器-今日新增网点",
        "name": "翻牌器-今日新增网点",
        "icon": "icon-flop",
        "top": 268.69,
        "left": 1612.32,
        "dataType": 1,
        "data": {
            "value": "12345"
        },
        "component": {
            "width": 309.86,
            "height": 124.93,
            "name": "flop",
            "prop": "flop",
            "option": {
                "type": "",
                "suffixText": "",
                "suffixTextAlign": "right",
                "suffixSplit": "",
                "suffixColor": "",
                "suffixFontSize": 0,
                "borderColor": "#fff",
                "borderWidth": 3,
                "backgroundBorder": "./img/border/border1.png",
                "fontSize": 42,
                "fontWeight": "normal",
                "splitx": 0,
                "splity": 0,
                "backgroundColor": "",
                "color": "#fff",
                "prefixTextAlign": "left",
                "prefixFontSize": 30,
                "row": false,
                "whole": true,
                "prefixColor": "",
                "width": 250,
                "textAlign": "left",
                "prefixSplitx": 3,
                "prefixSplity": 4,
                "suffixSplitx": 6,
                "suffixSplity": 4
            }
        },
        "index": 30,
        "zIndex": 30,
        "url": "/manage/service/actualData/2",
        "time": 30000,
        "dataMethod": "get",
        "display": false
    },
    {
        "title": "进度条-今日租赁量（组）",
        "name": "进度条-今日租赁量（组）",
        "icon": "icon-progress",
        "top": 435.5,
        "left": 1035.98,
        "data": {
            "label": "今日租赁量（组）",
            "value": 40,
            "data": 80
        },
        "component": {
            "width": 380,
            "height": 80,
            "option": {
                "type": "line",
                "color": "#2460dd",
                "fontSize": 30,
                "strokeWidth": 18,
                "fontWeight": "normal",
                "borderColor": "#2460dd",
                "width": 380,
                "height": 80,
                "suffixFontSize": 30,
                "suffixColor": "#2460dd",
                "suffixFontWeight": "bold",
                "FontWeight": "bold"
            },
            "name": "progress",
            "prop": "progress"
        },
        "index": 32,
        "zIndex": 32,
        "dataType": 1,
        "url": "/manage/service/actualData/num",
        "dataMethod": "get",
        "time": 61000
    },
    {
        "title": "进度条-今日租赁金额（元）",
        "name": "进度条-今日租赁金额（元）",
        "icon": "icon-progress",
        "top": 435.5,
        "left": 1487.49,
        "data": {
            "label": "今日租赁金额（元）",
            "value": 40,
            "data": 80
        },
        "component": {
            "width": 380,
            "height": 80,
            "option": {
                "type": "line",
                "color": "rgba(255, 140, 0, 1)",
                "fontSize": 30,
                "strokeWidth": 18,
                "fontWeight": "normal",
                "borderColor": "rgba(255, 140, 0, 1)",
                "width": 380,
                "height": 80,
                "FontWeight": "bold",
                "suffixFontSize": 30,
                "suffixFontWeight": "bold",
                "suffixColor": "rgba(255, 140, 0, 1)"
            },
            "name": "progress",
            "prop": "progress"
        },
        "index": 33,
        "zIndex": 33,
        "dataType": 1,
        "url": "/manage/service/actualData/rental",
        "dataMethod": "get",
        "time": 61000
    },
    {
        "title": "进度条-今日租赁押金（元）",
        "name": "进度条-今日租赁押金（元）",
        "icon": "icon-progress",
        "top": 557.73,
        "left": 1035.98,
        "data": {
            "label": "今日租赁押金（元）",
            "value": 40,
            "data": 80
        },
        "component": {
            "width": 380,
            "height": 80,
            "option": {
                "type": "line",
                "color": "rgba(250, 212, 0, 1)",
                "fontSize": 30,
                "strokeWidth": 18,
                "fontWeight": "normal",
                "borderColor": "rgba(250, 212, 0, 1)",
                "width": 380,
                "height": 80,
                "suffixFontSize": 30,
                "suffixColor": "rgba(250, 212, 0, 1)",
                "suffixFontWeight": "bold",
                "FontWeight": "bold"
            },
            "name": "progress",
            "prop": "progress"
        },
        "index": 32,
        "zIndex": 32,
        "dataType": 1,
        "url": "/manage/service/actualData/deposit",
        "dataMethod": "get",
        "time": 61000
    },
    {
        "title": "进度条-今日回收电池（只）",
        "name": "进度条-今日回收电池（只）",
        "icon": "icon-progress",
        "top": 560.5,
        "left": 1487.49,
        "data": {
            "label": "人数增涨-今日回收电池（只）",
            "value": 40,
            "data": 80
        },
        "component": {
            "width": 380,
            "height": 80,
            "option": {
                "type": "line",
                "color": "#564AA3",
                "fontSize": 30,
                "strokeWidth": 18,
                "fontWeight": "normal",
                "borderColor": "#564AA3",
                "width": 380,
                "height": 80,
                "FontWeight": "bold",
                "suffixFontSize": 30,
                "suffixFontWeight": "bold",
                "suffixColor": "#564AA3"
            },
            "name": "progress",
            "prop": "progress"
        },
        "index": 33,
        "zIndex": 33,
        "dataType": 1,
        "url": "/manage/service/actualData/single_num",
        "dataMethod": "get",
        "time": 61000
    },
    {
        "name": "折线图-车主用户",
        "title": "折线图-车主用户",
        "icon": "icon-line",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/service/dataTrend/user",
        "time": 30000,
        "component": {
            "width": 454.87,
            "height": 550.14,
            "name": "line",
            "prop": "line",
            "option": {
                "gridX": 102,
                "gridY": 87,
                "gridX2": 43,
                "gridY2": 74,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "lineWidth": 5,
                "xNameFontSize": 24,
                "yNameFontSize": 24,
                "labelShow": true,
                "labelShowFontSize": 24,
                "labelShowFontWeight": "bold",
                "yAxisInverse": false,
                "xAxisInverse": false,
                "xAxisShow": true,
                "yAxisShow": true,
                "xAxisSplitLineShow": false,
                "yAxisSplitLineShow": false,
                "barWidth": 30,
                "barRadius": 8,
                "barColor": [
                    {
                        "color1": "#83bff6",
                        "color2": "#188df0",
                        "postion": 90,
                        "$index": 0,
                        "_show": true
                    },
                    {
                        "color1": "#23B7E5",
                        "color2": "#564AA3",
                        "postion": 50,
                        "$index": 1,
                        "_show": true
                    }
                ],
                "barMinHeight": 2,
                "title": "车主用户",
                "titleFontSize": 30,
                "titlePostion": "left",
                "labelShowColor": "#fff",
                "titleShow": true,
                "titleColor": "rgba(30, 144, 255, 1)",
                "areaStyle": false,
                "tipFontSize": 24
            }
        },
        "left": 991.66,
        "top": 1565.05,
        "zIndex": 36,
        "index": 36
    },
    {
        "name": "折线图-合作网点",
        "title": "折线图-合作网点",
        "icon": "icon-line",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/service/dataTrend/service",
        "time": 60000,
        "component": {
            "width": 454.87,
            "height": 550.14,
            "name": "line",
            "prop": "line",
            "option": {
                "gridX": 102,
                "gridY": 87,
                "gridX2": 43,
                "gridY2": 74,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "lineWidth": 5,
                "xNameFontSize": 24,
                "yNameFontSize": 24,
                "labelShow": true,
                "labelShowFontSize": 24,
                "labelShowFontWeight": "bold",
                "yAxisInverse": false,
                "xAxisInverse": false,
                "xAxisShow": true,
                "yAxisShow": true,
                "xAxisSplitLineShow": false,
                "yAxisSplitLineShow": false,
                "barWidth": 30,
                "barRadius": 8,
                "barColor": [
                    {
                        "color1": "#83bff6",
                        "color2": "#188df0",
                        "postion": 90,
                        "$index": 0,
                        "_show": true
                    },
                    {
                        "color1": "#23B7E5",
                        "color2": "#564AA3",
                        "postion": 50,
                        "$index": 1,
                        "_show": true
                    }
                ],
                "barMinHeight": 2,
                "title": "合作网点",
                "titleFontSize": 30,
                "titlePostion": "left",
                "labelShowColor": "#fff",
                "titleShow": true,
                "titleColor": "rgba(30, 144, 255, 1)",
                "areaStyle": true,
                "category": false,
                "tipFontSize": 24
            }
        },
        "left": 1424.43,
        "top": 1562.28,
        "zIndex": 37,
        "index": 37
    },
    {
        "title": "翻牌器",
        "name": "翻牌器",
        "icon": "icon-flop",
        "top": 249.3,
        "left": 2994.37,
        "dataType": 1,
        "data": {
            "value": "12345"
        },
        "component": {
            "width": 730.9,
            "height": 426.86,
            "name": "flop",
            "prop": "flop",
            "option": {
                "type": "img",
                "suffixText": "",
                "suffixTextAlign": "",
                "suffixSplit": "",
                "suffixColor": "",
                "suffixFontSize": 0,
                "borderColor": "#fff",
                "borderWidth": 3,
                "backgroundBorder": "./img/border/border1.png",
                "fontSize": 42,
                "fontWeight": "normal",
                "splitx": 0,
                "splity": 0,
                "backgroundColor": "",
                "color": "#fff",
                "width": 281,
                "height": 133,
                "prefixFontSize": 30,
                "gridY": 0,
                "whole": true
            }
        },
        "index": 38,
        "zIndex": 38,
        "url": "/manage/kood/actualData",
        "dataMethod": "get",
        "time": 120000
    },
    {
        "title": "翻牌器",
        "name": "翻牌器",
        "icon": "icon-flop",
        "top": 811.61,
        "left": 2925.12,
        "dataType": 1,
        "data": {
            "value": "12345"
        },
        "component": {
            "width": 736.44,
            "height": 100,
            "name": "flop",
            "prop": "flop",
            "option": {
                "type": "",
                "suffixText": "",
                "suffixTextAlign": "",
                "suffixSplit": "",
                "suffixColor": "",
                "suffixFontSize": 0,
                "borderColor": "#fff",
                "borderWidth": 3,
                "backgroundBorder": "./img/border/border1.png",
                "fontSize": 30,
                "fontWeight": "normal",
                "splitx": 0,
                "splity": 0,
                "backgroundColor": "",
                "color": "#fff",
                "width": 320,
                "height": 50,
                "whole": true,
                "textAlign": "left",
                "prefixTextAlign": "left",
                "prefixSplity": 0,
                "row": true,
                "prefixSplitx": 1
            }
        },
        "index": 39,
        "zIndex": 39,
        "url": "/manage/kood/inventory",
        "dataMethod": "get",
        "time": 120000
    },
    {
        "name": "柱状图",
        "title": "柱状图",
        "icon": "icon-bar",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/kood/stockArea",
        "time": 120000,
        "component": {
            "width": 862.06,
            "height": 516.9,
            "name": "bar",
            "prop": "bar",
            "option": {
                "gridX": 88,
                "gridY": 37,
                "gridX2": 56,
                "gridY2": 74,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "xNameFontSize": 24,
                "yNameFontSize": 24,
                "labelShow": false,
                "labelShowFontSize": 20,
                "labelShowFontWeight": "bold",
                "yAxisInverse": false,
                "xAxisInverse": false,
                "xAxisShow": true,
                "yAxisShow": true,
                "xAxisSplitLineShow": false,
                "yAxisSplitLineShow": false,
                "barWidth": 16,
                "barRadius": 8,
                "barColor": [
                    {
                        "color1": "#83bff6",
                        "color2": "#188df0",
                        "postion": 90,
                        "$index": 0,
                        "_show": true
                    },
                    {
                        "color1": "rgba(144, 238, 144, 1)",
                        "color2": "rgba(0, 206, 209, 1)",
                        "postion": 50,
                        "$index": 1,
                        "_show": true
                    }
                ],
                "barMinHeight": 2,
                "labelShowColor": "#fff",
                "xAxisRotate": 40,
                "tipFontSize": 24
            }
        },
        "left": 2911.27,
        "top": 883.63,
        "zIndex": 40,
        "index": 40
    },
    {
        "title": "表格",
        "name": "表格",
        "icon": "icon-table",
        "top": 1551.2,
        "left": 2925.12,
        "dataType": 1,
        "data": [
            {
                "type1": "数据1",
                "type2": "数据2"
            },
            {
                "type1": "数据1",
                "type2": "数据2"
            }
        ],
        "component": {
            "width": 860.1,
            "height": 551.79,
            "name": "table",
            "prop": "table",
            "option": {
                "headerBackground": "rgba(0, 0, 0, 0.01)",
                "headerColor": "rgba(154, 168, 212, 1)",
                "headerTextAlign": "left",
                "bodyBackground": "rgba(0, 0, 0, 0.01)",
                "bodyColor": "rgba(154, 168, 212, 1)",
                "borderColor": "rgba(51, 65, 107, 1)",
                "bodyTextAlign": "left",
                "column": [
                    {
                        "label": "区域",
                        "prop": "site_name",
                        "width": "",
                        "$index": 0
                    },
                    {
                        "label": "回收量（吨）",
                        "prop": "weight",
                        "width": "",
                        "$index": 1
                    },
                    {
                        "label": "订单数（个）",
                        "prop": "order_num",
                        "width": "",
                        "$index": 2
                    }
                ],
                "menu": false,
                "align": "center",
                "headerAlign": "center",
                "header": false,
                "fontSize": 24,
                "count": 8,
                "scroll": true,
                "scrollTime": 2000,
                "scrollCount": 1,
                "index": true
            }
        },
        "zIndex": 41,
        "index": 41,
        "url": "/manage/kood/recycleRank",
        "time": 60000,
        "dataMethod": "get"
    }
];
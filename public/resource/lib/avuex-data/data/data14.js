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
        "name": "业务数据全景",
        "icon": "icon-text",
        "data": "业务数据全景",
        "component": {
            "width": 3836.73,
            "height": 116.48,
            "option": {
                "textAlign": "center",
                "fontSize": 80,
                "fontWeight": "bolder",
                "color": "rgba(30, 144, 255, 1)",
                "split": 10,
                "scroll": false
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
            "width": 1892.88,
            "height": 1986.55,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 1,
        "index": 1,
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 94.32,
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
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 88.78,
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
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 91.55,
        "top": 1458.99,
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
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
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
        "data": "./img/border/border1.png",
        "display": true
    },
    {
        "title": "滑动组件",
        "name": "滑动组件-区域分布",
        "icon": "icon-img",
        "top": 743.52,
        "left": 983.85,
        "child": {
            "index": [
                26,
                27
            ]
        },
        "component": {
            "width": 909.53,
            "height": 638.45,
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
        "data": "业务数据全景"
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1010,
        "top": 780,
        "zIndex": 10,
        "index": 10,
        "display": true
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
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1010,
        "top": 1495,
        "zIndex": 12,
        "index": 12,
        "display": true
    },
    {
        "title": "图片框",
        "name": "图片框-快点-业务概览",
        "icon": "icon-img",
        "top": 150,
        "left": 1930,
        "component": {
            "width": 1887.34,
            "height": 1983.78,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.05)"
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
                "fontWeight": "bold",
                "color": "#7eaae0",
                "title": "销售电池库存"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 2017.09,
        "top": 194.46,
        "zIndex": 14,
        "index": 14
    },
    {
        "title": "图片框",
        "name": "图片框-业务趋势",
        "icon": "icon-img",
        "top": 728.15,
        "left": 1932.4,
        "component": {
            "width": 959.2,
            "height": 698.72,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 15,
        "index": 15,
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1970,
        "top": 780,
        "zIndex": 16,
        "index": 16,
        "destruction": true
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
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 2014.32,
        "top": 1453.45,
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
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 2888.45,
        "top": 197.23,
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
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 2024.21,
        "top": 760.61,
        "zIndex": 16,
        "index": 16,
        "destruction": true
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
        "data": "./img/border/border1.png",
        "display": true
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
                "fontWeight": "bold",
                "color": "#7eaae0"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 2888.45,
        "top": 1453.45,
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
                "labelShowFontWeight": "bold",
                "yAxisInverse": false,
                "xAxisInverse": true,
                "xAxisShow": true,
                "yAxisShow": true,
                "xAxisSplitLineShow": false,
                "yAxisSplitLineShow": false,
                "barWidth": 23,
                "barRadius": 100,
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
                "titlePostion": "left",
                "titleShow": true,
                "titleColor": "#7eaae0",
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
                "gridX": 79,
                "gridY": 94,
                "gridX2": 82,
                "gridY2": 99,
                "nameColor": "#eee",
                "lineColor": "#eee",
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
                "barWidth": 23,
                "barRadius": 100,
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
                "titlePostion": "left",
                "titleShow": true,
                "titleColor": "#7eaae0",
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
        "top": 249.3,
        "left": 958.42,
        "component": {
            "width": 140,
            "height": 140,
            "name": "img",
            "prop": "img",
            "option": {
                "duration": 5,
                "opacity": 1,
                "rotate": false
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
        "top": 246.53,
        "left": 1434.86,
        "component": {
            "width": 140,
            "height": 140,
            "name": "img",
            "prop": "img",
            "option": {
                "duration": 5,
                "opacity": 1,
                "rotate": false
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
        "top": 271.46,
        "left": 1132.93,
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
        "top": 274.23,
        "left": 1615.09,
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
        "top": 449.35,
        "left": 1005.51,
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
                "fontWeight": "bold",
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
        "top": 452.12,
        "left": 1476.41,
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
                "fontWeight": "bold",
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
        "top": 588.2,
        "left": 1008.28,
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
                "fontWeight": "bold",
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
        "top": 590.97,
        "left": 1473.64,
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
                "fontWeight": "bold",
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
            "height": 622.16,
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
                "yNameFontSize": 22,
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
                "title": "用户增长趋势",
                "titleFontSize": 30,
                "titlePostion": "left",
                "labelShowColor": "#fff",
                "titleShow": true,
                "titleColor": "#7eaae0",
                "areaStyle": false,
                "tipFontSize": 24
            }
        },
        "left": 1013.82,
        "top": 1451.48,
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
            "height": 627.7,
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
                "title": "网点增长趋势",
                "titleFontSize": 30,
                "titlePostion": "left",
                "labelShowColor": "#fff",
                "titleShow": true,
                "titleColor": "#7eaae0",
                "areaStyle": true,
                "category": false,
                "tipFontSize": 24
            }
        },
        "left": 1424.43,
        "top": 1448.71,
        "zIndex": 37,
        "index": 37,
        "display": false
    },
    {
        "title": "翻牌器",
        "name": "翻牌器",
        "icon": "icon-flop",
        "top": 227.14,
        "left": 2900.19,
        "dataType": 1,
        "data": {
            "value": "12345"
        },
        "component": {
            "width": 777.99,
            "height": 482.26,
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
        "top": 797.76,
        "left": 1988.86,
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
        "time": 120000,
        "display": false
    },
    {
        "name": "柱状图-销售电池库存",
        "title": "柱状图",
        "icon": "icon-bar",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/kood/stockArea/sale",
        "time": 120000,
        "component": {
            "width": 853.75,
            "height": 588.92,
            "name": "bar",
            "prop": "bar",
            "option": {
                "gridX": 88,
                "gridY": 143,
                "gridX2": 56,
                "gridY2": 91,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "xNameFontSize": 21,
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
                "barWidth": 23,
                "barRadius": 100,
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
                "xAxisRotate": false,
                "tipFontSize": 24,
                "title": "销售电池库存",
                "titleFontSize": 30,
                "titleColor": "#7eaae0",
                "titleShow": true
            }
        },
        "left": 2429.29,
        "top": 747.9,
        "zIndex": 40,
        "index": 40,
        "display": true
    },
    {
        "title": "表格",
        "name": "表格",
        "icon": "icon-table",
        "top": 1520.73,
        "left": 2886.34,
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
    },
    {
        "title": "翻牌器-今日新增用户",
        "name": "翻牌器-今日新增用户",
        "icon": "icon-flop",
        "top": 278.59,
        "left": 205.86,
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
        "index": 42,
        "zIndex": 42,
        "url": "/api/renewal/rentSummaryNew/1",
        "time": 30000,
        "dataMethod": "get",
        "display": false
    },
    {
        "title": "图片",
        "name": "图片",
        "icon": "icon-img",
        "top": 260.13,
        "left": 40.07,
        "component": {
            "width": 140,
            "height": 140,
            "name": "img",
            "prop": "img",
            "option": {
                "duration": 5,
                "opacity": 1,
                "rotate": false
            }
        },
        "zIndex": 43,
        "index": 43,
        "data": "./img/source/source242.png"
    },
    {
        "title": "图片",
        "name": "图片",
        "icon": "icon-img",
        "top": 252.93,
        "left": 477.25,
        "component": {
            "width": 140,
            "height": 140,
            "name": "img",
            "prop": "img",
            "option": {
                "duration": 5,
                "opacity": 1,
                "rotate": false
            }
        },
        "zIndex": 44,
        "index": 44,
        "data": "./img/source/source242.png"
    },
    {
        "title": "翻牌器-今日新增用户",
        "name": "翻牌器-今日新增用户",
        "icon": "icon-flop",
        "top": 276.93,
        "left": 654.3,
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
        "index": 45,
        "zIndex": 45,
        "url": "/api/renewal/rentSummaryNew/2",
        "time": 30000,
        "dataMethod": "get",
        "display": false
    },
    {
        "title": "进度条-今日租赁量（组）",
        "name": "进度条-今日租赁量（组）",
        "icon": "icon-progress",
        "top": 449.36,
        "left": 71.27,
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
                "fontWeight": "bold",
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
        "index": 46,
        "zIndex": 46,
        "dataType": 1,
        "url": "/api/renewal/rentSummaryNew/3",
        "dataMethod": "get",
        "time": 61000
    },
    {
        "title": "进度条-今日租赁金额（元）",
        "name": "进度条-今日租赁金额（元）",
        "icon": "icon-progress",
        "top": 449.73,
        "left": 537.06,
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
                "fontWeight": "bold",
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
        "index": 47,
        "zIndex": 47,
        "dataType": 1,
        "url": "/api/renewal/rentSummaryNew/4",
        "dataMethod": "get",
        "time": 61000
    },
    {
        "title": "进度条-今日租赁押金（元）",
        "name": "进度条-今日租赁押金（元）",
        "icon": "icon-progress",
        "top": 586.9,
        "left": 74.41,
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
                "fontWeight": "bold",
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
        "index": 48,
        "zIndex": 48,
        "dataType": 1,
        "url": "/api/renewal/rentSummaryNew/5",
        "dataMethod": "get",
        "time": 61000
    },
    {
        "title": "进度条-今日回收电池（只）",
        "name": "进度条-今日回收电池（只）",
        "icon": "icon-progress",
        "top": 587.64,
        "left": 538.17,
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
                "fontWeight": "bold",
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
        "index": 49,
        "zIndex": 49,
        "dataType": 1,
        "url": "/api/renewal/rentSummaryNew/6",
        "dataMethod": "get",
        "time": 61000
    },
    {
        "name": "折线图",
        "title": "折线图",
        "icon": "icon-line",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/api/renewal/rentTrend",
        "time": 60000,
        "component": {
            "width": 952.8,
            "height": 566.78,
            "name": "line",
            "prop": "line",
            "option": {
                "gridX": 105,
                "gridY": 50,
                "gridX2": 80,
                "gridY2": 100,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "lineWidth": 5,
                "xNameFontSize": 27,
                "yNameFontSize": 27,
                "labelShow": false,
                "labelShowFontSize": 14,
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
                        "color2": null,
                        "postion": 90,
                        "$index": 0,
                        "_show": true
                    },
                    {
                        "color1": "rgba(197, 42, 11, 1)",
                        "color2": null,
                        "postion": 50,
                        "$index": 1,
                        "_show": true
                    },
                    {
                        "color1": "rgba(255, 215, 0, 1)",
                        "color2": "",
                        "postion": 0,
                        "$index": 2
                    }
                ],
                "barMinHeight": 2,
                "legendPostion": "center",
                "legendFontSize": 27,
                "xAxisRotate": false,
                "legend": true
            }
        },
        "left": 50.04,
        "top": 825.22,
        "zIndex": 50,
        "index": 50
    },
    {
        "title": "翻牌器",
        "name": "翻牌器",
        "icon": "icon-flop",
        "top": 221.9,
        "left": 2012.67,
        "dataType": 1,
        "data": {
            "value": "12345"
        },
        "component": {
            "width": 777.99,
            "height": 490.57,
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
        "index": 51,
        "zIndex": 51,
        "url": "/api/renewal/koodSummaryNew",
        "dataMethod": "get",
        "time": 120000
    },
    {
        "title": "表格",
        "name": "业务统计",
        "icon": "icon-table",
        "top": 1529.35,
        "left": 56.87,
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
                        "prop": "name",
                        "width": "",
                        "$index": 0
                    },
                    {
                        "label": "租赁总数",
                        "prop": "total_num",
                        "width": "",
                        "$index": 1
                    },
                    {
                        "label": "租赁金额",
                        "prop": "rental",
                        "width": "",
                        "$index": 2
                    },
                    {
                        "label": "租赁押金",
                        "prop": "total_deposit",
                        "width": "",
                        "$index": 3
                    },
                    {
                        "label": "回收电池",
                        "prop": "retrie_num",
                        "width": "",
                        "$index": 4
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
        "zIndex": 52,
        "index": 52,
        "url": "/api/renewal/rankRentRenewal",
        "time": 60000,
        "dataMethod": "get"
    },
    {
        "title": "表格",
        "name": "表格",
        "icon": "icon-table",
        "top": 1520.3,
        "left": 1973.73,
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
                        "prop": "name",
                        "width": "",
                        "$index": 0
                    },
                    {
                        "label": "销售额(万元)",
                        "prop": "money",
                        "width": "",
                        "$index": 1
                    },
                    {
                        "label": "订单数(个)",
                        "prop": "order_num",
                        "width": "",
                        "$index": 2
                    },
                    {
                        "label": "电池数(组)",
                        "prop": "bar_num",
                        "width": "",
                        "$index": 3
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
        "zIndex": 53,
        "index": 53,
        "url": "/api/renewal/koodTable",
        "time": 60000,
        "dataMethod": "get"
    },
    {
        "name": "电池销售趋势",
        "title": "折线图",
        "icon": "icon-line",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/api/renewal/saleOrder",
        "time": 600000,
        "component": {
            "width": 830.4,
            "height": 540,
            "name": "line",
            "prop": "line",
            "option": {
                "gridX": 105,
                "gridY": 98,
                "gridX2": 80,
                "gridY2": 100,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "lineWidth": 5,
                "xNameFontSize": 27,
                "yNameFontSize": 27,
                "labelShow": false,
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
                        "color1": "rgba(199, 21, 133, 1)",
                        "color2": null,
                        "postion": 90,
                        "$index": 0,
                        "_show": true
                    },
                    {
                        "color1": "#83bff6",
                        "color2": null,
                        "postion": 50,
                        "$index": 1,
                        "_show": true
                    },
                    {
                        "color1": "rgba(197, 42, 11, 1)",
                        "color2": "",
                        "postion": 0,
                        "$index": 2
                    },
                    {
                        "color1": "rgba(255, 215, 0, 1)",
                        "color2": "",
                        "postion": 0,
                        "$index": 3
                    }
                ],
                "barMinHeight": 2,
                "legendPostion": "center",
                "legendFontSize": 27,
                "xAxisRotate": false,
                "legend": true,
                "title": "电池销售趋势",
                "titleFontSize": 30,
                "titleColor": "#7eaae0",
                "titleShow": true
            }
        },
        "left": 1960.8,
        "top": 842.4,
        "zIndex": 54,
        "index": 54,
        "display": true
    },
    {
        "name": "电池回收趋势",
        "title": "折线图",
        "icon": "icon-line",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/api/renewal/retrieOrder",
        "time": 600000,
        "component": {
            "width": 830.4,
            "height": 540,
            "name": "line",
            "prop": "line",
            "option": {
                "gridX": 105,
                "gridY": 98,
                "gridX2": 80,
                "gridY2": 100,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "lineWidth": 5,
                "xNameFontSize": 27,
                "yNameFontSize": 27,
                "labelShow": false,
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
                        "color1": "rgba(199, 21, 133, 1)",
                        "color2": null,
                        "postion": 90,
                        "$index": 0,
                        "_show": true
                    },
                    {
                        "color1": "#83bff6",
                        "color2": null,
                        "postion": 50,
                        "$index": 1,
                        "_show": true
                    },
                    {
                        "color1": "rgba(197, 42, 11, 1)",
                        "color2": "",
                        "postion": 0,
                        "$index": 2
                    },
                    {
                        "color1": "rgba(255, 215, 0, 1)",
                        "color2": "",
                        "postion": 0,
                        "$index": 3
                    }
                ],
                "barMinHeight": 2,
                "legendPostion": "center",
                "legendFontSize": 27,
                "xAxisRotate": false,
                "legend": true,
                "title": "电池回收趋势",
                "titleFontSize": 30,
                "titlePostion": "left",
                "titleShow": true,
                "subTitleColor": "",
                "titleColor": "#7eaae0"
            }
        },
        "left": 1958.4,
        "top": 837.6,
        "zIndex": 55,
        "index": 55,
        "display": true
    },
    {
        "title": "滑动组件",
        "name": "滑动组件-电池销售回收趋势",
        "icon": "icon-img",
        "top": 742.28,
        "left": 2874.48,
        "child": {
            "index": [
                54,
                55
            ]
        },
        "component": {
            "width": 911.2,
            "height": 627.37,
            "name": "slide",
            "prop": "slide",
            "option": {
                "autoplay": true,
                "delay": 3000,
                "title": "回收电池库存"
            }
        },
        "zIndex": 56,
        "index": 56,
        "data": "./img/border/border1.png"
    },
    {
        "title": "图片框",
        "name": "图片框",
        "icon": "icon-img",
        "top": 155.12,
        "left": 5.54,
        "component": {
            "width": 1915.57,
            "height": 1982.05,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.05)"
            }
        },
        "zIndex": -1,
        "index": 57,
        "data": "./img/border/border1.png"
    },
    {
        "name": "实时时间",
        "title": "实时时间",
        "icon": "icon-datetime",
        "top": 49.86,
        "left": 3155.03,
        "zIndex": 58,
        "component": {
            "width": 319.25,
            "height": 50,
            "name": "datetime",
            "prop": "datetime",
            "option": {
                "format": "yyyy年MM月dd日",
                "color": "#fff",
                "textAlign": "left",
                "fontSize": 40,
                "fontWeight": "bold"
            }
        },
        "index": 58
    },
    {
        "name": "实时时间",
        "title": "实时时间",
        "icon": "icon-datetime",
        "top": 49.86,
        "left": 3487.43,
        "zIndex": 59,
        "component": {
            "width": 250,
            "height": 50,
            "name": "datetime",
            "prop": "datetime",
            "option": {
                "format": "day",
                "color": "#fff",
                "textAlign": "left",
                "fontSize": 40,
                "fontWeight": "bold"
            }
        },
        "index": 59
    },
    {
        "name": "柱状图-回收电池库存",
        "title": "柱状图",
        "icon": "icon-bar",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/kood/stockArea/recycle",
        "time": 120000,
        "component": {
            "width": 947.93,
            "height": 608.31,
            "name": "bar",
            "prop": "bar",
            "option": {
                "gridX": 79,
                "gridY": 150,
                "gridX2": 91,
                "gridY2": 81,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "xNameFontSize": 21,
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
                "barWidth": 23,
                "barRadius": 100,
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
                "tipFontSize": 24,
                "title": "回收电池库存",
                "titleFontSize": 30,
                "titleShow": true,
                "titleColor": "#7eaae0",
                "labelShowColor": "#fff",
                "tipColor": "#fff"
            }
        },
        "left": 1980.55,
        "top": 742.36,
        "zIndex": 60,
        "index": 60,
        "display": true
    },
    {
        "title": "滑动组件",
        "name": "滑动组件-电池销售回收区域分布",
        "icon": "icon-img",
        "top": 745.05,
        "left": 1966.45,
        "child": {
            "index": [
                40,
                60
            ]
        },
        "component": {
            "width": 911.2,
            "height": 632.91,
            "name": "slide",
            "prop": "slide",
            "option": {
                "autoplay": true,
                "delay": 8000,
                "title": "回收电池库存"
            }
        },
        "zIndex": 61,
        "index": 61,
        "data": "./img/border/border1.png",
        "display": false
    }
];
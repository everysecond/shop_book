var config = {
    width: 1920,
    height: 1080,
    scale: 1,
    mark: {},
    url: 'http://yapi.demo.qunar.com/mock/9013',
    backgroundImage: './img/bg/bg18.jpg',
    backgroundColor: '#0d2027',
    query: {}
};
var list = [
    {
        "name": "柱状图",
        "title": "柱状图",
        "icon": "icon-bar",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/lease/data/userArea",
        "time": 60000,
        "component": {
            "width": 434.11,
            "height": 299.76,
            "name": "bar",
            "prop": "bar",
            "option": {
                "gridX": 57,
                "gridY": 29,
                "gridX2": 54,
                "gridY2": 26,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "xNameFontSize": 10,
                "yNameFontSize": 10,
                "labelShow": true,
                "labelShowFontSize": 8,
                "labelShowFontWeight": 300,
                "yAxisInverse": false,
                "xAxisInverse": false,
                "xAxisShow": true,
                "yAxisShow": true,
                "xAxisSplitLineShow": false,
                "yAxisSplitLineShow": false,
                "barWidth": 9,
                "barRadius": 3,
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
                "category": true,
                "labelShowColor": "white",
                "title": "用户分布",
                "titleShow": true,
                "titleColor": "#fff",
                "titleFontSize": 15,
                "subTitleFontSize": 15
            }
        },
        "left": 554.61,
        "top": 765.89,
        "zIndex": 0,
        "index": 0
    },
    {
        "name": "饼图",
        "title": "饼图",
        "icon": "icon-pie",
        "dataType": 1,
        "dataMethod": "get",
        "time": 5000,
        "url": "https://yapi.avuejs.com/pie",
        "data": [],
        "resize": false,
        "component": {
            "width": 588,
            "height": 340,
            "name": "pie",
            "prop": "pie",
            "option": {
                "labelShow": true,
                "barColor": [
                    {
                        "color1": "#83bff6",
                        "$index": 0
                    },
                    {
                        "color1": "#23B7E5",
                        "$index": 1
                    },
                    {
                        "color1": "rgba(154, 168, 212, 1)",
                        "$index": 2
                    },
                    {
                        "color1": "#188df0",
                        "$index": 3
                    },
                    {
                        "color1": "#564AA3",
                        "$index": 4
                    }
                ]
            }
        },
        "left": 638.01,
        "top": 20.85,
        "zIndex": 1,
        "index": 1,
        "destruction": true
    },
    {
        "name": "饼图",
        "title": "饼图",
        "icon": "icon-pie",
        "dataType": 1,
        "dataMethod": "get",
        "time": 60000,
        "url": "/manage/lease/data/userAuth",
        "data": [],
        "resize": false,
        "component": {
            "width": 390.62,
            "height": 288.57,
            "name": "pie",
            "prop": "pie",
            "option": {
                "labelShow": true,
                "barColor": [
                    {
                        "color1": "#83bff6",
                        "$index": 0
                    },
                    {
                        "color1": "#23B7E5",
                        "$index": 1
                    },
                    {
                        "color1": "rgba(154, 168, 212, 1)",
                        "$index": 2
                    },
                    {
                        "color1": "#188df0",
                        "$index": 3
                    },
                    {
                        "color1": "#564AA3",
                        "$index": 4
                    }
                ],
                "labelShowFontSize": 12,
                "labelShowFontWeight": "normal",
                "radius": true,
                "roseType": false,
                "sort": false,
                "notCount": false,
                "labelShowColor": "rgba(30, 144, 255, 1)",
                "legend": false,
                "switchTheme": false,
                "legendPostion": "right",
                "legendOrient": "vertical",
                "tipFontSize": 12,
                "titleShow": true,
                "title": "实名认证率",
                "nameColor": "",
                "lineColor": "",
                "tipColor": "rgba(30, 144, 255, 1)",
                "theme": "macarons",
                "titleColor": "#fff",
                "subTitleFontSize": 15,
                "legendFontSize": 12,
                "titleFontSize": 15,
                "fontSize": 12
            }
        },
        "left": 966.05,
        "top": 763.11,
        "zIndex": 2,
        "index": 2
    },
    {
        "name": "柱状图",
        "title": "柱状图",
        "icon": "icon-bar",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/lease/data/userArea",
        "time": 60000,
        "component": {
            "width": 543.92,
            "height": 245.55,
            "name": "bar",
            "prop": "bar",
            "option": {
                "gridX": 72,
                "gridY": 50,
                "gridX2": 56,
                "gridY2": 62,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "xNameFontSize": 10,
                "yNameFontSize": 10,
                "labelShow": true,
                "labelShowFontSize": 8,
                "labelShowFontWeight": 300,
                "yAxisInverse": false,
                "xAxisInverse": false,
                "xAxisShow": true,
                "yAxisShow": true,
                "xAxisSplitLineShow": false,
                "yAxisSplitLineShow": false,
                "barWidth": 10,
                "barRadius": 2,
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
                "title": "网点分布",
                "titlePostion": "center",
                "titleShow": false,
                "titleColor": "#fff",
                "labelShowColor": "#fff"
            }
        },
        "left": 1353.86,
        "top": 72.28,
        "zIndex": 3,
        "index": 3
    },
    {
        "name": "柱状图",
        "title": "柱状图",
        "icon": "icon-bar",
        "dataType": 1,
        "dataMethod": "get",
        "url": "/manage/service/incomeRank",
        "time": 60000,
        "component": {
            "width": 595.35,
            "height": 349.8,
            "name": "bar",
            "prop": "bar",
            "option": {
                "gridX": 109,
                "gridY": 50,
                "gridX2": 56,
                "gridY2": 34,
                "nameColor": "#eee",
                "lineColor": "#eee",
                "xNameFontSize": 10,
                "yNameFontSize": 11,
                "labelShow": true,
                "labelShowFontSize": 8,
                "labelShowFontWeight": 300,
                "yAxisInverse": false,
                "xAxisInverse": false,
                "xAxisShow": true,
                "yAxisShow": true,
                "xAxisSplitLineShow": false,
                "yAxisSplitLineShow": false,
                "barWidth": 9,
                "barRadius": 2,
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
                "title": "网点收益排行",
                "titlePostion": "center",
                "titleShow": false,
                "titleColor": "#fff",
                "category": true,
                "subTitleFontSize": 12,
                "tipFontSize": 12,
                "labelShowColor": "#fff"
            }
        },
        "left": 1362.2,
        "top": 284.95,
        "zIndex": 3,
        "index": 3
    },
    {
        "title": "表格",
        "name": "表格",
        "icon": "icon-table",
        "top": 689.44,
        "left": 1364.98,
        "dataType": 1,
        "data": [
            {
                "name": "河南",
                "service_num": 263,
                "income": "2205587.00"
            },
            {
                "name": "广西",
                "service_num": 46,
                "income": "1047796.00"
            },
            {
                "name": "江西",
                "service_num": 53,
                "income": "767649.00"
            },
            {
                "name": "浙江",
                "service_num": 91,
                "income": "616746.00"
            },
            {
                "name": "福建",
                "service_num": 45,
                "income": "430685.00"
            },
            {
                "name": "湖南",
                "service_num": 49,
                "income": "345822.00"
            },
            {
                "name": "安徽",
                "service_num": 73,
                "income": "287590.00"
            },
            {
                "name": "江苏",
                "service_num": 62,
                "income": "286508.00"
            },
            {
                "name": "山东",
                "service_num": 42,
                "income": "141094.00"
            },
            {
                "name": "湖北",
                "service_num": 28,
                "income": "108104.00"
            },
            {
                "name": "台湾",
                "service_num": 1,
                "income": "2885.00"
            }
        ],
        "component": {
            "width": 491.66,
            "height": 368.19,
            "name": "table",
            "prop": "table",
            "option": {
                "headerBackground": "rgba(0, 0, 0, 0.01)",
                "headerColor": "rgba(154, 168, 212, 1)",
                "headerTextAlign": "center",
                "bodyBackground": "rgba(0, 0, 0, 0.01)",
                "bodyColor": "rgba(154, 168, 212, 1)",
                "borderColor": "rgba(51, 65, 107, 1)",
                "bodyTextAlign": "left",
                "column": [
                    {
                        "label": "区域",
                        "prop": "jg",
                        "width": "",
                        "$index": 0
                    },
                    {
                        "label": "网点数",
                        "prop": "service_num",
                        "width": "",
                        "$index": 1
                    },
                    {
                        "label": "收益",
                        "prop": "income",
                        "width": "",
                        "$index": 2
                    }
                ],
                "menu": false,
                "align": "center",
                "headerAlign": "center",
                "header": false,
                "count": 10,
                "scroll": true,
                "scrollTime": 2000,
                "scrollCount": 1,
                "index": true,
                "columnShow": false,
                "othColor": "#11274c",
                "nthColor": "",
                "headerBackgroud": "#11274c",
                "fontSize": 12
            }
        },
        "zIndex": 5,
        "index": 5,
        "url": "/manage/service/incomeAreaRank",
        "dataMethod": "get",
        "display": false,
        "time": 60000
    },
    {
        "title": "图片框",
        "name": "图片框",
        "icon": "icon-img",
        "top": 725.58,
        "left": 556,
        "component": {
            "width": 789.13,
            "height": 60.77,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": -1,
        "index": 10,
        "data": "./img/banner/banner2.png"
    },
    {
        "title": "文本框",
        "name": "文本框",
        "icon": "icon-text",
        "data": "用户分析",
        "component": {
            "width": 81.93,
            "height": 20.81,
            "option": {
                "textAlign": "center",
                "fontSize": 19,
                "fontWeight": "normal",
                "color": "#fff"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 583.8,
        "top": 745.04,
        "zIndex": 7,
        "index": 7
    },
    {
        "title": "图片框",
        "name": "图片框",
        "icon": "icon-img",
        "top": 63.94,
        "left": 1360.81,
        "component": {
            "width": 462.48,
            "height": 60.77,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": -1,
        "index": 10,
        "data": "./img/banner/banner1.png"
    },
    {
        "title": "文本框",
        "name": "文本框",
        "icon": "icon-text",
        "data": "网点分布",
        "component": {
            "width": 81.93,
            "height": 20.81,
            "option": {
                "textAlign": "center",
                "fontSize": 19,
                "fontWeight": "normal",
                "color": "#fff"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1395.56,
        "top": 82.01,
        "zIndex": 7,
        "index": 7
    },
    {
        "title": "图片框",
        "name": "图片框",
        "icon": "icon-img",
        "top": 628.28,
        "left": 1356.64,
        "component": {
            "width": 462.48,
            "height": 60.77,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": -1,
        "index": 10,
        "data": "./img/banner/banner1.png"
    },
    {
        "title": "文本框",
        "name": "文本框",
        "icon": "icon-text",
        "data": "网点收益统计",
        "component": {
            "width": 134.75,
            "height": 20.81,
            "option": {
                "textAlign": "center",
                "fontSize": 19,
                "fontWeight": "normal",
                "color": "#fff"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1388.61,
        "top": 646.35,
        "zIndex": 7,
        "index": 7
    },
    {
        "title": "图片框",
        "name": "图片框",
        "icon": "icon-img",
        "top": 286.34,
        "left": 1359.42,
        "component": {
            "width": 490.28,
            "height": 60.77,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": -1,
        "index": 10,
        "data": "./img/banner/banner1.png"
    },
    {
        "title": "文本框",
        "name": "文本框",
        "icon": "icon-text",
        "data": "网点收益排行",
        "component": {
            "width": 137.53,
            "height": 20.81,
            "option": {
                "textAlign": "center",
                "fontSize": 19,
                "fontWeight": "normal",
                "color": "#fff"
            },
            "name": "text",
            "prop": "text"
        },
        "left": 1385.83,
        "top": 303.02,
        "zIndex": 7,
        "index": 7,
        "display": false
    },
    {
        "title": "图片框",
        "name": "图片框",
        "icon": "icon-img",
        "top": 125.1,
        "left": 640.79,
        "component": {
            "width": 620.94,
            "height": 582.02,
            "name": "imgborder",
            "prop": "imgborder",
            "option": {
                "backgroundColor": "rgba(180, 181, 198, 0.1)"
            }
        },
        "zIndex": 8,
        "index": 8,
        "data": "./img/samiao.png",
        "destruction": true
    },
    {
        "title": "图片",
        "name": "图片",
        "icon": "icon-img",
        "top": 133.44,
        "left": 612.99,
        "component": {
            "width": 651.52,
            "height": 683.49,
            "name": "img",
            "prop": "img",
            "option": {
                "duration": 3000,
                "opacity": 0,
                "rotate": true
            }
        },
        "zIndex": -1,
        "index": 9,
        "data": "./img/samiao.png"
    },
    {
        "title": "文本框",
        "name": "文本框",
        "icon": "icon-text",
        "data": "CPS租点动力数据全景",
        "component": {
            "width": 700.48,
            "height": 50,
            "option": {
                "textAlign": "center",
                "fontSize": 40,
                "fontWeight": "bolder",
                "color": "#fff",
                "split": 5
            },
            "name": "text",
            "prop": "text"
        },
        "left": 597.7,
        "top": 11.12,
        "zIndex": 16,
        "index": 16
    },
    {
        "name": "实时时间",
        "title": "实时时间",
        "icon": "icon-datetime",
        "top": 13.9,
        "left": 1554.02,
        "zIndex": 17,
        "component": {
            "width": 443.21,
            "height": 50,
            "name": "datetime",
            "prop": "datetime",
            "option": {
                "format": "数据更新时间：yyyy年MM月dd日",
                "color": "#fff",
                "textAlign": "left",
                "fontSize": 24,
                "fontWeight": "normal"
            }
        },
        "index": 17
    },
    {
        "name": "实时时间",
        "title": "实时时间",
        "icon": "icon-datetime",
        "top": 9.73,
        "left": 4.17,
        "zIndex": 18,
        "component": {
            "width": 302.82,
            "height": 50,
            "name": "datetime",
            "prop": "datetime",
            "option": {
                "format": "yyyy年MM月dd日 hh:mm:ss",
                "color": "#fff",
                "textAlign": "left",
                "fontSize": 24,
                "fontWeight": "normal"
            }
        },
        "index": 18
    },
    {
        "name": "实时时间",
        "title": "实时时间",
        "icon": "icon-datetime",
        "top": 9.73,
        "left": 286.34,
        "zIndex": 19,
        "component": {
            "width": 88.76,
            "height": 50,
            "name": "datetime",
            "prop": "datetime",
            "option": {
                "format": "day",
                "color": "#fff",
                "textAlign": "left",
                "fontSize": 24,
                "fontWeight": "normal"
            }
        },
        "index": 19
    }
];
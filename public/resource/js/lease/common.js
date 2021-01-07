var colorArr = [
    '#2fc25b', '#1890ff', '#fbd437', '#27727B',
    '#87f7cf', '#36cbcb', '#72ccff', '#f7c5a0',
    '#0098d9', '#2b821d', '#e87c25', '#e01f54'
];
var colorNormal = {
    color: function (params) {
        return colorArr[params.dataIndex]
    },
    label:{
        show: true,
        formatter: '{b}:{c}({d}%)'
    },
    labelLine :{show:true}
};
var tips = $(".layui-icon-tips");
tips.mouseover(function () {
    layer.tips($(this).data("tips"), this, {tips: [2, "rgb(107, 106, 106)"], time: 0});
});
tips.mouseout(function () {
    layer.closeAll();
});
<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/7 9:42
 */

namespace Modules\Manage\Services;

use \Exception as Exception;

class ChartService
{
    /**
     * 饼图
     * @param array $data :数据源
     * @param array $arr :分类依据数组
     */
    public function pieChartFormat(array $data, array $arr)
    {
        $format = [];
        $array = [];
        foreach ($arr as $item) {
            if (isset($data[$item]) && $data[$item] > 0) {
                $array[] = $item;
                $format[] = [
                    "value" => isset($data[$item]) ? $data[$item] : 0,
                    "name"  => $item
                ];
            }
        }
        $return = ["ageArr" => $array, "ageData" => $format];
        if (!count($format)) {
            $return = [
                "ageArr"  => ["抱歉！暂无数据"],
                "ageData" => [[
                    "value" => 0,
                    "name"  => "抱歉！暂无数据"
                ]]
            ];
        }
        return $return;
    }

    /**
     * 地图类型
     * @param array $areaData :数据源
     * @param $agentsMap :区域map
     * @param $provincesArr :所有省份arr
     * @return array
     */
    public function mapChartFormat(array $areaData, $agentsMap, $provincesArr)
    {
        $max = max($areaData);
        $replaceArea = [];
        foreach ($areaData as $id => $num) {
            $replaceArea[$agentsMap[$id]] = $num;
        }
        $areaDataFormat = [];
        foreach ($provincesArr as $name) {
            $value = isset($replaceArea[$name]) ? $replaceArea[$name] : 0;
            $areaDataFormat[] = [
                "value" => $value,
                "name"  => $name
            ];
        }
        return ["max" => $max, "min" => 0, "areaData" => $areaDataFormat];
    }

    /**
     * 折线图类型
     * @param array $data :数据源
     * @param array $legend :lineNameArr
     * @param array $xAxis :横坐标数据Arr
     * @return array
     */
    public function lineChart(array $data, array $legend, array $xAxis)
    {
        $series = [];
        $defaultNumArr = [];
        foreach ($xAxis as $day) {
            $defaultNumArr[$day] = 0;
        }
        foreach ($legend as $code => $value) {
            $series[$code] = [
                "name"       => $value,
                "type"       => 'line',
                "stack"      => $value,
                "symbolSize" => 6,
                "symbol"     => 'circle',
                "data"       => $defaultNumArr,
//                "smooth"     => true,//线条形状 默认折线 smooth 为半圆弧线
//                "itemStyle"  => ["normal" => ["areaStyle" => ["type" => 'default']]], //面积图
            ];
            foreach ($data as $datum) {
                if (isset($datum[$code])) {
                    $series[$code]["data"][$datum["date"]] = $datum[$code];
                }
            }
            $series[$code]["data"] = array_values($series[$code]["data"]);
        }
        return ["legend" => array_values($legend), "xAxis" => $xAxis, "series" => array_values($series)];
    }

    /**
     * 活跃事件折线图类型
     * @param array $data :数据源
     * @param array $legend :lineNameArr
     * @param array $xAxis :横坐标数据Arr
     * @param array $hiddenLegend :默认隐藏的列
     * @return array
     */
    public function lineActiveChart(array $data, array $legend, array $xAxis, array $hiddenLegend)
    {
        $series = [];
        $defaultNumArr = [];
        foreach ($xAxis as $day) {
            $defaultNumArr[$day] = 0;
        }
        foreach ($legend as $code => $value) {
            $series[$code] = [
                "name"       => $value,
                "type"       => 'line',
                "stack"      => $value,
                "symbolSize" => 6,
                "symbol"     => 'circle',
                "data"       => $defaultNumArr,
            ];
            foreach ($data as $datum) {
                if ($datum["page_url"] == $code) {
                    $series[$code]["data"][$datum["date"]] = $datum["num"];
                }
            }
            $series[$code]["data"] = array_values($series[$code]["data"]);
        }

        $days = [];
        foreach ($xAxis as $xAxi) {
            $days[] = date("m-d",strtotime($xAxi));
        }

        return [
            "legend"       => array_values($legend),
            "xAxis"        => $days,
            "series"       => array_values($series),
            "hiddenLegend" => $hiddenLegend
        ];
    }

    /**
     * 简单柱状图类型
     * @param array $data :数据源
     * @param array $xAxis :横坐标数据
     * @return array
     */
    public function histogramChart(array $data, array $xAxis)
    {
        $seriesData = [];
        foreach ($xAxis as $xAxi) {
            $seriesData[$xAxi] = 0;
        }
        foreach ($data as $k => $datum) {
            if (isset($seriesData[$k])) {
                $seriesData[$k] = $datum;
            }
        }

        return ["xAxis" => $xAxis, "seriesData" => array_values($seriesData)];
    }

    /**
     * 简单柱状图类型
     * @param array $data :数据源
     * @param array $xAxis :横坐标数据
     * @return array
     */
    public function histogramActiveChart(array $data, array $xAxis)
    {
        $seriesData = [];
        foreach ($xAxis as $k=>$xAxi) {
            $seriesData[$k] = 0;
        }
        foreach ($data as $datum) {
            if (isset($seriesData[$datum["page_url"]])) {
                $seriesData[$datum["page_url"]] = $datum["num"];
            }
        }

        return ["xAxis" => array_values($xAxis), "seriesData" => array_values($seriesData)];
    }

    /**
     * 堆叠柱状图
     * @param array $data :数据源
     * @param array $xAxis :横坐标
     * @return array
     */
    public function histogramPileChart(array $data, array $xAxis)
    {
        $main = [0];
        $supply = [0];
        foreach ($xAxis as $k => $xAxi) {
            $main[0] += isset($data[$k]) ? $data[$k] : 0;
            if ($k > 0) {
                $main[] = isset($data[$k]) ? $data[$k]*1 : 0;
            }
        }
        $total = $main[0];
        foreach ($main as $k => $value) {
            if ($k > 0) {
                $supply[$k] = $total - $main[$k];
                $total -= $main[$k];
            }
        }

        return ["xAxis" => array_values($xAxis), "seriesDataOne" => $supply, "seriesDataTwo" => $main];
    }
}
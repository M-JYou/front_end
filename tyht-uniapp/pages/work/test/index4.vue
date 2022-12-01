<template>
  <view class="container">
    <echarts :option="option" style="width: 100%; height: 300px;" @click="echartsClick"></echarts>
  </view>
</template>

<script>
  import echarts from '@/components/echarts/echarts.vue'
  export default {
    components: {
      echarts
    },
    data() {
      return {
        option: {},
        option1: {
          // title: {
          //   text: 'Stacked Line',
          //   x: 'center'
          // },
          tooltip: {
            trigger: 'axis'
          },
          legend: {
            selectedMode: 'single',
            data: ['Email', 'Union Ads', 'Video Ads', 'Direct', 'Search Engine']
          },
          grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '15%',
            containLabel: true
          },
          toolbox: {
            feature: {
              saveAsImage: {}
            }
          },
          xAxis: {
            type: 'category',
            boundaryGap: false,
            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
          },
          yAxis: {
            type: 'value'
          },
          series: [{
              name: 'Email',
              type: 'line',
              stack: 'Total',
              data: [120, 132, 101, 134, 90, 230, 210]
            },
            {
              name: 'Union Ads',
              type: 'line',
              stack: 'Total',
              data: [220, 182, 191, 234, 290, 330, 310]
            },
            {
              name: 'Video Ads',
              type: 'line',
              stack: 'Total',
              data: [150, 232, 201, 154, 190, 330, 410]
            },
            {
              name: 'Direct',
              type: 'line',
              stack: 'Total',
              data: [320, 332, 301, 334, 390, 330, 320]
            },
            {
              name: 'Search Engine',
              type: 'line',
              stack: 'Total',
              data: [820, 932, 901, 934, 1290, 1330, 1320]
            }
          ]
        },
        option2: {
          title: {
            text: '123',
            x: 'center',
            y: ''
          },
          notMerge: true, // 自定义变量：true代表不合并数据，比如从折线图变为柱形图则需设置为true；false或不写代表合并
          tooltip: {
            trigger: 'axis',
            positionStatus: true,
            formatterStatus: true, // 自定义变量：是否格式化tooltip，设置为false时下面三项均不起作用
            formatterUnit: '元', // 自定义变量：数值后面的单位
            formatFloat2: true, // 自定义变量：是否格式化为两位小数
            formatThousands: true // 自定义变量：是否添加千分位
          },
          legend: {
            data: ['', '']
          },
          grid: {
            top: '8%',
            left: '8%',
            right: '8%',
            bottom: '8%',
            innerHeight: '50%',
            containLabel: true
          },
          xAxis: [{
            name: '位移（m）',
            nameLocation: 'middle', //轴位置
            nameGap: 26, //name名字与轴线间距
            type: 'value',
            axisLine: {
              onZero: false
            }
          }],
          yAxis: [{
            name: '载荷（KN）',
            nameLocation: 'middle', //轴位置
            nameGap: 26, //name名字与轴线间距
            type: 'value',
            axisLine: {
              onZero: false
            }
          }],
          series: [{
            id: 'a',
            type: 'line',
            // smooth: true,
            symbolSize: 0,
            data: [
              [0, 24.15],
              [0.02, 26.02],
              [0.08, 28.1],
              [0.18, 30.01],
              [0.3, 31.67],
              [0.46, 32.99],
              [0.66, 33.92],
              [0.88, 34.53],
              [1.12, 34.99],
              [1.42, 35.39],
              [1.72, 35.85],
              [2.06, 36.39],
              [2.42, 36.99],
              [2.82, 37.63],
              [3.24, 38.28],
              [3.68, 38.94],
              [4.14, 39.62],
              [4.64, 40.33],
              [5.14, 41.1],
              [5.68, 41.93],
              [6.22, 42.82],
              [6.8, 43.74],
              [7.38, 44.65],
              [8, 45.35],
              [8.62, 45.75],
              [9.26, 45.8],
              [9.9, 45.56],
              [10.58, 45.19],
              [11.26, 44.8],
              [11.94, 44.52],
              [12.64, 44.31],
              [13.34, 44.26],
              [14.04, 44.36],
              [14.76, 44.51],
              [15.46, 44.71],
              [16.2, 44.84],
              [16.92, 44.92],
              [17.64, 44.85],
              [18.38, 44.69],
              [19.1, 44.46],
              [19.84, 44.21],
              [20.56, 44.06],
              [21.28, 43.99],
              [22, 44.03],
              [22.72, 44.14],
              [23.46, 44.27],
              [24.16, 44.36],
              [24.88, 44.38],
              [25.58, 44.31],
              [26.28, 44.2],
              [26.98, 44.07],
              [27.68, 43.97],
              [28.36, 43.91],
              [29.04, 43.89],
              [29.72, 43.94],
              [30.38, 44.02],
              [31.04, 44.13],
              [31.68, 44.22],
              [32.34, 44.26],
              [32.98, 44.28],
              [33.6, 44.26],
              [34.22, 44.24],
              [34.84, 44.19],
              [35.44, 44.19],
              [36.04, 44.18],
              [36.64, 44.18],
              [37.2, 44.24],
              [37.78, 44.26],
              [38.34, 44.32],
              [38.9, 44.36],
              [39.44, 44.39],
              [39.98, 44.42],
              [40.5, 44.43],
              [41.02, 44.46],
              [41.52, 44.47],
              [42.02, 44.48],
              [42.52, 44.49],
              [42.98, 44.5],
              [43.46, 44.5],
              [43.92, 44.49],
              [44.36, 44.46],
              [44.8, 44.46],
              [45.22, 44.46],
              [45.62, 44.49],
              [46.02, 44.51],
              [46.4, 44.51],
              [46.78, 44.51],
              [47.14, 44.51],
              [47.48, 44.52],
              [47.82, 44.54],
              [48.14, 44.55],
              [48.44, 44.56],
              [48.72, 44.58],
              [48.98, 44.6],
              [49.24, 44.63],
              [49.46, 44.64],
              [49.68, 44.63],
              [49.86, 44.59],
              [50.02, 44.52],
              [50.18, 44.42],
              [50.3, 44.28],
              [50.4, 44.08],
              [50.48, 43.76],
              [50.52, 43.14],
              [50.56, 42.1],
              [50.56, 40.68],
              [50.54, 38.91],
              [50.48, 37.06],
              [50.4, 35.33],
              [50.28, 33.85],
              [50.14, 32.64]
            ]
          }]
        }
      };
    },

    onLoad() {
      this.option = this.option1
    },
    methods: {
      /**
       * 点击事件
       * @param {Object} params
       */
      echartsClick(params) {
        console.log('点击数据', params)
      },
      /**
       * 切换数据
       */
      // updateClick() {
      //   if (this.option === this.option2) {
      //     this.option = this.option3
      //   } else {
      //     this.option = this.option2
      //   }
      // }
    }
  };
</script>

<style>
  .container {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    box-sizing: border-box;
  }
</style>

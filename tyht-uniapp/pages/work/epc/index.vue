<template>
  <view class="container">
    <!-- 列表 -->
    <view v-for="(item, index) in epcData" :key="index">
      <view v-if="item.length==0">
        <fq-empty empty-text="暂无数据"></fq-empty>
      </view>
      <!-- 列表样式 -->
      <view class="order-item" @click="epcDetails(item)" v-else>
        <view class="order-top-view">
          <view class="order-store-name">{{ item.name }}</view>
        </view>
        <view class="order-content">
          <view class="">
            <view class="order-name">进度：{{ item.finished }}%</view>
          </view>
          <view class="price">
            <text style="font-size: 14rpx;">{{dateFormat(item.createTime)}}</text>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
  import {
    getEpcRecord,
  } from '@/api/work/epc/epc.js'
  export default {
    data() {
      return {
        scrollH: 660,
        // 是否有下一页
        hasNext: null,
        formData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        epcData: [],
        // 子级页面路径
        URL: '/pages/work/epc/epcDetails/index'
      };
    },
    methods: {
      getData() {
        getEpcRecord(this.formData).then(res => {
          this.hasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.formData.pageNum == 1) {
              this.epcData = [];
            }
            // this.epcData.push(...newList)
            this.epcData = [...this.epcData, ...newList]
            // console.log(this.epcData.length);
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      epcDetails(item) {
        uni.navigateTo({
          url: this.URL + "?item=" + JSON.stringify(item)
        })
      },

      // 格式化时间
      dateFormat(time) {
        let date = new Date(time);
        let year = date.getFullYear();
        // 在日期格式中，月份是从0开始的，因此要加0，使用三元表达式在小于10的前面加0，以达到格式统一  如 09:11:05
        let month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        let day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        let hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        let minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        let seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
        // 拼接
        // return year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
        return year + "-" + month + "-" + day;
      }
    },
    onLoad() {
      uni.getSystemInfo({
        success: res => {
          // console.log('wuwuFQ:', res);
          this.scrollH = res.windowHeight;
          // #ifdef MP
          this.scrollH -= 44;
          // #endif
          // console.log('wuwuFQ:', this.scrollH);
        }
      });
      this.getData();
      setTimeout(() => {}, 1000);
      uni.startPullDownRefresh();
    },

    onReachBottom() {
      if (this.hasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.formData.pageNum++;
        this.getData() //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };
    },

    onPullDownRefresh() {
      this.formData.pageNum = 1;
      this.getData();
      setTimeout(() => {
        uni.stopPullDownRefresh();
      }, 1000);
    }
  };
</script>

<style>
  .order-item {
    background-color: #fff;
    padding: 20rpx;
    border-radius: 15rpx;
    margin: 15rpx;
  }

  .order-top-view {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10rpx;
  }

  .order-store-name {
    font-size: 16px;
    font-weight: 500;
  }

  .order-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
</style>

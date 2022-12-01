<template>
  <view class="hrListItem">
    <view class="wrapbox" v-for="(item,i) in biddingData" :key="item.id">
      <view v-if="item==''">暂时没有数据</view>
      <view class="wrap" @click="handleToDetails(item)" v-else>
        <text class="wrapLeft">项目名称：{{item.name}}</text>
        <text class="wrapRight">{{dateFormat(item.createTime)}}</text>
      </view>
      <view class="line"></view>
    </view>
  </view>
</template>
<script>
  import {
    getManageBidding
  } from '@/api/work/manage/manage.js'
  export default {
    data() {
      return {
        // 是否有下一页
        hasNext: null,
        formData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        biddingData: [],
        URL: '/pages/work/manage/manageBidding/indexDetails'
      }
    },

    methods: {
      getData() {
        getManageBidding(this.formData).then(res => {
          this.hasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.formData.pageNum == 1) {
              this.biddingData = [];
            }
            this.biddingData = [...this.biddingData, ...newList]
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      handleToDetails(item) {
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
  }
</script>
<style>
  .hrList {
    display: flex;

    flex-direction: column;
    width: 100%;
  }

  .hrName {
    font-size: 30rpx;
    font-weight: 500;
    line-height: 30rpx;
    margin: 30rpx 0 10rpx 30rpx;
    flex-wrap: wrap;
  }

  .wrapbox {
    width: 100%;
  }

  .wrap {
    display: flex;
    color: gray;
    font-size: 26rpx;
    margin: 16rpx 30rpx 16rpx;
  }

  .wrapLeft {
    flex-grow: 1;

  }

  .wrapRight {
    /* display: */
    flex-grow: 1;
    font-size: 12rpx;
    text-align: right;
    flex-wrap: nowrap;
  }

  .line {
    display: flex;
    width: 100%;
    margin: 0 auto;
    height: 0.1rpx;
    background-color: lightgray;
  }
</style>

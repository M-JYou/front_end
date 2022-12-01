<template>
  <view class="container">
    <!-- 列表 -->
    <view v-for="(item, index) in personData" :key="index">
      <view v-if="item.length==0">
        <fq-empty empty-text="暂无数据"></fq-empty>
      </view>
      <!-- 列表样式 -->
      <view v-else>
        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">姓名</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.name}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">资质类型</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.cerType}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">资质等级</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.cerLevel}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">有效期至</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{dateFormat(item.cerValidUntil)}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">电话</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;" @click="()=>telFun(item.phone)">{{item.phone}}</text>
            </view>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
  export default {
    data() {
      return {
        scrollH: 660,
        formData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        personData: []
      }
    },
    methods: {
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
      },

      telFun(e) {
        const res = uni.getSystemInfoSync();
        // 模态框
        if (res.platform == 'ios') {
          uni.makePhoneCall({
            phoneNumber: e,
            success() {
              console.log('拨打成功');
            },
            fail() {
              console.log('拨打失败');
            }
          })
        } else {
          uni.showActionSheet({
            itemList: [e, '呼叫'],
            success: function(res) {
              console.log(res, 3333);
              if (res.tapIndex == 1) {
                uni.makePhoneCall({
                  phoneNumber: e,
                })
              }
            }
          })
        }
      }
    },

    onLoad(options) {
      uni.getSystemInfo({
        success: res => {
          this.scrollH = res.windowHeight;
          // #ifdef MP
          this.scrollH -= 44;
          // #endif
        }
      });
      this.personData = [JSON.parse(options.item1)]
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

  .order-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
</style>

<template>
  <view class="container">
    <text class="common-title">工程记录</text>
    <!-- 列表 -->
    <view v-for="(item, i) in epcItem" :key="item.id">

      <view v-if="item.length==0">
        <fq-empty empty-text="暂无数据"></fq-empty>
      </view>
      <!-- 列表样式 -->
      <view class="order-item" v-else>
        <view class="order-content">
          <view>
            <view class="order-name">工程名称</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.name}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">是否签订合同</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{statusText[item.isSignContract]}}</text> <text class="view"
              @click="view()">查看</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">工程描述</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.description}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">工程地点</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.address}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">工程总价</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numFilter(item.contractPrice)}}元</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">工程利润</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numFilter(item.contractProfit)}}元</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">是否开工</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{isStart[item.isStart]}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">开工日期</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{dateFormat(item.startDate)}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">计划交付日期</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{dateFormat(item.planDeliveryDate)}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">质保期</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.guaranteePeriod}}年</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">质保金</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.qualityGuaranteeDeposit}}%</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">预计竣工日期</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{dateFormat(item.planEndDate)}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">工程负责人</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.epcResponsible}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">工程负责人电话</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.epcResponsiblePhone}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">安全负责人</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.patrolResponsible}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">安全负责人电话</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.patrolResponsiblePhone}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">协作单位负责人</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.cooperativeUnitResponsible}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">协作单位负责人电话</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.cooperativeUnitResponsiblePhone}}</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">工程进度</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{item.finished}}%</text>
          </view>
        </view>
      </view>
    </view>
    <text class="common-title">工程结算</text>
    <view v-for="(item,j) in statementData" :key="item.id">
      <view class="order-item">
        <view class="order-content">
          <view>
            <view class="order-name">设备结算</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numConverter(item.equSettlementAmount)}}元</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">设备运输结算</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numConverter(item.equTransportSettlementAmount)}}元</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">材料结算</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numConverter(item.materialSettlementAmount)}}元</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">材料运输结算</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numConverter(item.materialTransportSettlementAmount)}}元</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">人工结算</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numConverter(item.manpowerSettlementAmount)}}元</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">其他结算</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numConverter(item.otherSettlementAmount)}}元</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">结算总金额</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numConverter(item.settlementAmount)}}元</text>
          </view>
        </view>

        <view class="order-content">
          <view>
            <view class="order-name">工程余额</view>
          </view>
          <view>
            <text style="font-size: 14rpx;">{{numConverter(item.epcRemainingSum)}}元</text>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
  import {
    getEpcStatement
  } from '@/api/work/epc/epc.js'
  export default {
    data() {
      return {
        arrPhotos: [],
        objPhotos: [],
        // baseUrl: 'http://180.76.141.244:8080',
        baseUrl: 'http://test.tianyuekeji.ltd',
        statementData: [],
        epcItem: [],
        statusText: {
          "0": "未签订",
          "1": "已签订"
        },
        isStart: {
          '0': '未开工',
          '1': '已开工'
        }
      };
    },
    methods: {
      // 字符串数据拼接转化
      dataConverter() {
        // 如果上级页面传递数据中有图片信息，则继续执行，否则停止
        if (this.epcItem[0].contractAttachment !== null) {
          let arrCount = this.epcItem[0].contractAttachment.length
          for (let i = 0; i < arrCount; i++) {
            let photos = this.epcItem[0].contractAttachment[i]
            let strPhotos = this.baseUrl + photos.split(',');
            this.arrPhotos.push(strPhotos)
          }
          let arr = this.arrPhotos;
          let objPhotos = arr.reduce((s, a, c) => {
            s.push({
              id: c + 1,
              src: a
            });
            return s;
          }, [])
          this.objPhotos = objPhotos
        }
      },

      // 点击预览图片
      view() {
        // 如果上级页面传递数据中有图片信息，则继续执行，否则停止
        if (this.epcItem[0].contractAttachment !== null) {
          let photoList = this.objPhotos.map(item => {
            return item.src;
          });
          uni.previewImage({
            urls: photoList
          });
        }
      },

      // 单位换算
      numConverter(val) {
        return val / 100
      },

      // 小数保留两位
      numFilter(value) {
        // 截取当前数据到小数点后两位
        let realVal = parseFloat(value / 1000000).toFixed(2)
        return realVal
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
    onLoad(options) {
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
      this.epcItem = [JSON.parse(options.item)];
      this.dataConverter();
      getEpcStatement({
        epcId: this.epcItem[0].id
      }).then(res => {
        this.statementData = res.data.list
      })
    }
  };
</script>

<style>
  .common-title {
    display: flex;
    font-size: 34rpx;
    margin: 20rpx 0 0 35rpx;
  }

  .order-name {
    margin: 15rpx 0 15rpx;
  }

  .order-item {
    background-color: whitesmoke;
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

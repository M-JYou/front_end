<template>
  <view class="fq-empty">
    <image src="/static/images/work/nodata.png"></image>
    <text class="">{{emptyText}}</text>
  </view>
</template>

<script>
  export default {
    props: {
      // 检测类型 + 其他验证
      emptyText: {
        type: String,
        default: '什么都没有',
      }
    },
  }
</script>

<style>
  .fq-empty {
    display: flex;
    background-color: #eee;
    height: 431rpx;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 16px;
  }

  .fq-empty image {
    width: 100rpx;
    height: 100rpx;
    margin: 5rpx auto 0;
  }
</style>

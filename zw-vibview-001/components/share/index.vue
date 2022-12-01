<template>
  <view class="share" v-if="show">
    <view class="share_box">
		<view class="share_label">分享至</view>
      <view class="share_list">
       <view class="share_item" @click="shareType(1)">
          <image src="/static/share/1.png" mode="scaleToFill" />
          <view class="tit">微信</view>
        </view>
        <view class="share_item" @click="shareType(2)">
          <image src="/static/share/2.png" mode="scaleToFill" />
          <view class="tit">朋友圈</view>
        </view>
        <view class="share_item" @click="shareType(5)">
          <image src="/static/share/9.png" mode="scaleToFill" />
          <view class="tit">复制</view>
        </view>
        <view class="share_item" @click="shareType(6)">
          <image src="/static/share/10.png" mode="scaleToFill" />
          <view class="tit">更多</view>
        </view>
      </view>
      <view class="share_cale" @click="() => {show = false}">取消</view>
    </view>
  </view>
</template>

<script>
export default {
  data () {
    return {
      show: false
    }
  },
  created () {},
  mounted () {},
  props: {
    data: {
      type: Object,
      default: () => {}
    }
  },
  methods: {
    shareType (t) {
      var shareObj = {
        href: this.data.href||"",
        success:(res)=>{
          console.log("success:" + JSON.stringify(res));
        },
        fail:(err)=>{
          console.log("fail:" + JSON.stringify(err));
        },
        summary: this.data.desc
      }
      switch (t) {
        case 1:
          shareObj.provider="weixin";
          shareObj.scene="WXSceneSession";
          shareObj.type=0;
          shareObj.imageUrl = this.data.imgUrl||"";
          uni.share(shareObj);
          break;
        case 2:
          shareObj.provider="weixin";
          shareObj.scene="WXSenceTimeline";
          shareObj.type=0;
          shareObj.imageUrl=this.data.imgUrl||"";
          shareObj.title=this.data.desc;
          uni.share(shareObj);
          break;
        case 3:
          shareObj.provider="sinaweibo";
          shareObj.type=0;
          shareObj.imageUrl=this.data.imgUrl||"";
          uni.share(shareObj);
          break;
        case 4:
          shareObj.provider="qq";
          shareObj.type=1;
          shareObj.title=this.data.title||"";
          shareObj.href=this.data.href||"";
          shareObj.summary = this.data.desc || "";
          uni.share(shareObj);
          break;
        case 5:
          uni.setClipboardData({
            data: this.data.copy,
            complete() {
              uni.showToast({
                title: "已复制到剪贴板",
				icon:'none'
              })
            }
          })
          break;
        case 6:
          plus.share.sendWithSystem({
            type:"web",
            title:this.data.title||"",
            thumbs:[this.data.imgUrl||""],
            href:this.data.href||"",
            content: this.data.desc||"",
          })
          break;
      };
	  this.$emit('confirm',this.data.id)
    }
  }
}
</script>
<style scoped lang="scss">
  @import 'index.scss';
</style>

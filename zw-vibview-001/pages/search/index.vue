<template>
	<view class="pages">
		<uni-nav-bar :status-bar="true" :fixed='true' :backgroundColor="'#ffffff'" :border="false" color="#333333" left-icon="back" leftWidth="40rpx" rightWidth="60rpx" @clickLeft="back" rightText="取消" @clickRight="keyword!=''?cleanInput():back()">
			<view class="search_cen divflex">
				<input v-model="keyword" @confirm="search" placeholder="请输入内容" placeholder-class="placinput" />
				
				<image src="/static/icon/search.png" class="icon_search" @click="search"></image>
			</view>
		</uni-nav-bar>
		<view v-if="!is_search" class="data">
			<view class="search-list margintop">
				<view v-if="(list!='')" class="search-title divflex">
					<view class="title">历史搜索</view>
					<view class="operation divflex">
						<view v-if="list.length > 6" @click="lookMore" class="tet">{{moreTitle}}</view>
						<!-- <view @click="clear" class="tet">清空</view> -->
					</view>
				</view>
				<!-- <view class="info-list">
					<view  v-for="(item,index) in moreLength" class="recommend-list divflex" :key="index">
						<view @click="clicksearch(list[index])" class="tetx">{{list[index]}}</view>
						<image @click="close(index)" src="/static/icon/closex.png" class="closeimg"></image>
					</view>
					<view v-if="list.length > 6" @click="lookMore" class="more_btn">{{moreTitle}}</view>
				</view> -->
				<view class="recommend-list divflex">
					<view @click="clicksearch(item)" v-for="(item,index) in moreLength" class="recommend-list-li divflex" :key="index">
						{{list[index]}}
					</view>
					
				</view>
				
				<view v-if="list.length!=0" class="delview divflex">
					<image src="/static/icon/del.png" class="img"></image>
					<view @click="clear" class="tet">清空</view>
				</view>
			</view>
			<view v-if="(hotList!='')" class="search-list">
				<view class="search-title divflex">
					<view class="title">推荐搜索</view>
				</view>
				<view class="recommend-list divflex">
					<view @click="clicksearch(item)" v-for="(item,index) in hotList" class="recommend-list-li divflex" :key="index">
						{{item}}
					</view>
				</view>
			</view>
		</view>
		<block v-if="is_search">
		<view v-if="informationList&&informationList.length!=0" class="creditInformation">
			<view class="information-data">
				<view @click="openPage('/pages/articleDetails/index','id='+item.id)" v-for="(item,index) in informationList" class="list" :key="index">
					<view class="listTop divflex">
						<view @click.stop="token?(userInfo.id!=item.members_id?openPage('/pages/individual/index','id='+item.members_id):openPage('/pages/individual/index','id='+item.members_id)):login()" class="listTop-left divflex">
							<image :src="item.avatar" class="head"></image>
							<view class="inf">
								<view class="name">{{item.name}}</view>
								<view v-if="!item.is_official" class="date">{{item.create_time}} <!-- . 成都市 --></view>
								<view v-if="item.is_official" class="attestation divflex">
									<image src="/static/legend/guan.png" class="img"></image>
									<view class="tit">官方认证</view>
								</view>
							</view>
						</view>
						<block v-if="userInfo.id!=item.members_id">
						<view @click.stop="token?getfollow(item.members_id,index,item.member_follow?false:true):login()" class="listTop-righgt">
							<view class="follow">{{item.member_follow?'已关注':'关注'}}</view>
						</view>
						</block>
					</view>
					<view class="listInfo">
						<view class="infotetx">
							<view class="tet showone">{{item.text_content}}</view>
							<!-- {{item.text_content&&item.text_content.length>20&&!item.istext?(item.text_content.substring(0,20)+'...'):item.text_content}} -->
						</view>
						<view v-if="item.type==1" class="infoImg divflex">
							<image v-if="index_i<4" v-for="(item_i,index_i) in item.resource_infos" :key="index_i" :src="item_i" class="imgarr"></image>
							<view v-if="item.resource_infos.length>4" class="dian divflex">
								<view class="tet"></view>
								<view class="tet max"></view>
								<view class="tet"></view>
							</view>
						</view>
						<view v-if="item.type==2" class="infoVideo">
							<image :src="item.abbreviate_img" class="video"></image>
							<view class="multimedias-marks divflex">
								<image src="/static/play.png" mode="aspectFill">
								</image>
							</view>
						</view>
					</view>
					<view class="listComment divflex">
						<view class="commentLeft">
							{{item.create_time}}
						</view>
						<view class="commentRight divflex">
							<view class="li divflex">
								<image src="/static/icon/comment.png" class="img"></image>
								<view class="tet">{{item.comment_number}}</view>
							</view>
							<view class="li divflex">
								<image src="/static/icon/fabulous.png" class="img"></image>
								<view class="tet">{{item.give_number}}</view>
							</view>
							<view class="li divflex">
								<image src="/static/icon/share.png" class="img"></image>
								<view class="tet">{{item.share_number}}</view>
							</view>
						</view>
					</view>
				</view>
				<uni-load-more iconType="snow" :status="isLoadMore" :content-text="contentText" />
			</view>
			
		</view>
		<view v-if="!informationList||informationList.length==0" class="data administrator exam">
			<view class="details">
				<!-- <image src="/static/no_1.png" class="successimg"></image> -->
				<view class="title">暂无数据</view>
				<view class="subhead">没有关于 " {{keyword1}} " 的内容</view>
			</view>
		</view>
		</block>
		<zdyDialog
			:istitle="false"
			showText="是否清空搜索历史？"
			confirmText="确定"
			v-show="showDialog"
			@confirmBtn="clickConfirm"
			@cancelBtn="()=>{showDialog=false}"
		></zdyDialog>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list:[],
				is_clear:false,
				hotList:[],
				keyword:'',
				pIndex:1,
				pSize:10,
				total:0,
				informationList:[],
				showDialog: false,
				moreLength: 0,
				moreTitle: '展开全部',
				isLoadMore: 'more',//loading,noMore
				contentText: {
					contentdown: '查看更多',
					contentrefresh: '加载中',
					contentnomore: '没有更多啦'
				},
				is_search:false,
				keyword1:'',
				userInfo:'',
				token:''
			}
		},
		onLoad() {
			this.token = uni.getStorageSync("token")
			this.userInfo = uni.getStorageSync("userInfo")
			this.list = uni.getStorageSync('searchList')?uni.getStorageSync('searchList'):[];
			this.moreLength = this.list.length > 6 ? 6 : this.list.length
			
		},
		onReachBottom() {
			
			if (this.isLoadMore != 'no-more') {
				this.isLoadMore = "loading"
				setTimeout(() => {
					this.search(this.keyword,1)
				}, 1200);
			}
		},
		methods: {
			//单条删除搜索历史
			close(index){
				this.list.splice(index,1)
				this.setSearchList()
			},
			//清空搜索记录
			clear(){
				this.showDialog = true
			},
			clicksearch(i){
				this.keyword = i
				this.search(i,1)
			},
			search(){
				
				if(this.keyword){
					this.is_search=true
					this.keyword1 = this.keyword
					var x = true
					if(this.list.length!=0){
						for(var i = 0;i<this.list.length;i++){
							if(this.list[i] == this.keyword){
								x = false
							}
						}
					}
					if(x){
						this.list.splice(0,0,this.keyword)
					}
					this.$findApi.getFindInformationDataLists({
						page: this.pIndex,
						limit: this.pSize,
						text_content: this.keyword
					}).then((res) => {
						if (res.code == 200) {
							this.informationList = this.pIndex==1 ? res.data.data :this.informationList.concat( res.data.data)
							
							this.$forceUpdate()
							if (res.data.data.length < this.pSize) {
								this.isLoadMore = "no-more"
							} else {
								this.pIndex++
								this.isLoadMore = "more"
							}
							if (this.informationList.length == 0) {
								this.isLoadMore = "no-more"
							}
						} else{
							this.$.toast(res.msg);
						}
					})
					this.setSearchList()
				}
			},
			/**
			 * 跳转点击
			 */
			openPage(url,data){
				uni.navigateTo({
					url: url+(data?'?'+data:'')
				})
			},
			login(){
				this.$.toast('请先登录')
				setTimeout(()=>{
					uni.navigateTo({
						url:'/pages/login/index'
					})
				},800)
			},
			//内容展开收起
			opentext(idx){
				if(this.informationList.length!=0){
					let indexs = this.informationList.findIndex(item => {
						return item.istext&&item.istext==true
					})
					if(indexs != -1){
						this.$set(this.informationList[indexs],'istext',false)
					}
					if(indexs!=idx){
						this.$set(this.informationList[idx],'istext',true)
					}
				}else{
					this.$set(this.informationList[idx],'istext',this.informationList[idx].istext?false:true)
				}
				this.$forceUpdate()
			},
			//关注其他用户or取消关注
			getfollow(id,idx,type){
				var url = type?this.$commonApi.followMembers:this.$commonApi.cancelFollowMembers
				url({
					follow_members_id: id
				}).then(res => {
					if (res.code == 200) {
						this.$.toast(res.msg)
						this.is_follow = false
						this.$set(this.informationList[idx],'member_follow',type?true:false)
						this.informationList.forEach((item,index)=>{
							if(item.members_id==id){
								this.$set(item,'member_follow',type?true:false)
							}
						})
						this.$forceUpdate()
					} else this.$.toast(res.msg)
				})
			},
			back() {
				uni.navigateBack({
					delta: 1
				})
			},
			cleanInput () {
				this.keyword = ''
				this.keyword1 = ''
				this.is_search = false
				this.informationList = []
			},
			clickConfirm () {
				this.list = []
				this.setSearchList()
				this.showDialog = false
			},
			lookMore () {
				if (this.moreLength == 6) {
					this.moreLength = this.list.length
					this.moreTitle = '收起部分'
				} else {
					this.moreLength = 6
					this.moreTitle = '展开全部'
				}
			},
			setSearchList () {
				uni.setStorageSync('searchList', this.list);
				this.moreLength = this.list.length > 6 ? 6 : this.list.length
			}
		}
	}
</script>

<style scoped lang="scss">
	@import 'index.scss';
</style>
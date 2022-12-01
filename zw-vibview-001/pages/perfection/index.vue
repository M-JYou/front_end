<template>
	<view class="pages">
		<view class="content">
			<view class="form">
				<view class="headline">
					<view class="headercontent">
						<view class="imgboxs">
							<image class="headerimgs" src="../../static/tabbars/user001.png" mode=""></image>
						</view>
						<view class="headernames">
							<view class="">{{personalData.name}}</view>
							<view class="">{{personalData.rolename}}</view>
						</view>
					</view>
				</view>
				<uni-forms ref="baseForm" class="forminfo">

					<view class="telinfo-box">
						<view class="telinfo divflex codeinfo">
							<u-icon name="account" color="#ffffff" size="35"></u-icon>
							<view class="tettype">姓名 :</view>
							<input class="inputts" name="username" v-model="personalData.realname"
								placeholderStyle="color: #a6a7ab;font-size: 16px;" placeholder="请输入您的姓名" />
						</view>
						<view class="telinfo divflex codeinfo">
							<view class="iconimgs">
								<u-icon name="phone" color="#ffffff" size="35"></u-icon>
							</view>
							<view class="tettype">手机 :</view>
							<input class="inputts" name="telphone" v-model="personalData.telphone" 
								placeholderStyle="color: #a6a7ab;font-size: 16px;" placeholder="请输入您的手机号" />
						</view>
						<view class="telinfo divflex codeinfo">
							<u-icon name="email" color="#ffffff" size="35"></u-icon>
							<view class="tettype">邮箱 :</view>
							<input class="inputts" name="email" v-model="personalData.email"
								placeholderStyle="color: #a6a7ab;font-size: 16px;" placeholder="请输入您的邮箱号" />
						</view>
						<view class="telinfo divflex codeinfo">
							<u-icon name="weixin-fill" color="#ffffff" size="35"></u-icon>
							<view class="tettype">微信 :</view>
							<input class="inputts" type="" v-model="personalData.wechat"
								placeholderStyle="color: #a6a7ab;font-size: 16px;" placeholder="请输入您的微信账号" />
						</view>
					</view>
				</uni-forms>
				<button class="btnli btn-login action" form-type="submit" type="primary" @tap="submit()">保 存</button>
			</view>
		</view>
	</view>
</template>
<script>
	import {
		minelList,
		submitList
	} from "@/api/api.js"
	export default {
		data() {
			return {
					reg: /^(13|14|15|16|17|18|19)[0-9]\d{8}$/,
					regs:/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/,
				realname: '',
				telphone: '',
				email: '',
				wechat: '',

				personalData: {
					name: "",
					rolename: "",
					realname: "",
					telphone: "",
					email: "",
					wechat: ""
				},
				timer: null,
			};
		},
		onLoad(options) {
			//拿传过来的id
			this.id = options.id
			this.getDetails()
		},
		onShow() {

		},
		methods: {
			// 获取默认数据
			getDetails() {
				let id = this.id
				minelList(id).then(res => {
					console.log(res, "zl")
					if (res.statusCode == 200) {
						this.personalData = res.data
					}
				})
			},
			// 提交
			submit(e) {
				const params = {
					Origin: "app",
					RequestData: {
						id: this.id,
						realname: this.personalData.realname,
						telphone: this.personalData.telphone,
						email: this.personalData.email,
						wechat: this.personalData.wechat
					},
				}
				const {telphone} =  this.personalData;
				const {email} =  this.personalData;
				const {realname}=this.personalData    
		 if(!this.regs.test(email)){
		 			 uni.showToast({
		 			 	title:'邮箱格式不正确！',
		 				icon:'none'
		 			 })
		 			return 
		 }
		 if(!this.reg.test(telphone)){
		 			 uni.showToast({
		 			 	title:'请输入正确的手机号码',
		 				icon:'none'
		 			 })
		 			 return
		 }
		 else{
			 console.log(params.RequestData.realname, "得到code")
				submitList(JSON.stringify(params)).then(res => {
					console.log(res, "bian")
					if (res.statusCode == 200) {
						uni.showToast({
							title: "修改成功",
						})

						setTimeout(() => {
							uni.navigateBack({
								delta: 1
							})
						},1000)


					}

				})
		 }

			},



			niex() {
				this.is_type = true
			},
			back() {
				uni.navigateBack({
					delta: 1
				})
			},
		}
	};
</script>

<style lang="scss" scoped>
	@import "index.scss";
</style>

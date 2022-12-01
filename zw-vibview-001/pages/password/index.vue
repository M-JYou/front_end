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
							<text>账号 : </text>
							<text> {{passData.name}}</text>
						</view>
					</view>
				</view>
				<uni-forms ref="baseForm" :modelValue="form1" class="forminfo">
					<view class="telinfo-box">
						<view class="telinfo divflex codeinfo">
							<u-icon name="lock-open" color="#ffffff" size="20px"></u-icon>
							<view class="tettype">原密码 :</view>
							<input class="inputts" type="password" v-model="form1.password"
								placeholderStyle="color: #a6a7ab;font-size: 16px;" placeholder="请输入原密码" />
						</view>
						<view class="telinfo divflex codeinfo">
							<view class="iconimgs">
								<u-icon name="bag" color="#ffffff" size="20px"></u-icon>
							</view>
							<view class="tettype">新密码 :</view>
							<input class="inputts" type="password" v-model="form1.password1"
								placeholderStyle="color: #a6a7ab;font-size: 16px;" placeholder="请输入新密码" />
						</view>
						<view class="telinfo divflex codeinfo">
							<u-icon name="lock" color="#ffffff" size="20px"></u-icon>
							<view class="tettype">请确认 :</view>
							<input class="inputts" type="password" v-model="form1.password2" 
								placeholderStyle="color: #a6a7ab;font-size: 16px;" placeholder="请再次输入新密码" />
						</view>
					</view>
				</uni-forms>
				<view @click="submit" class="btnli btn-login action">提  交</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		gainPasswd,
		SubmitPassword
	} from '@/api/api.js'
	export default {
		data() {
			return {
				oldpwd: '',
				newpwd: '',
				passData: '', //获取数据
				form1: {
					password: '', //原密码
					password1: '', //新密码
					password2: '' //新密码确认
				},
				timer: null,
			};
		},
		onLoad(options) {
			this.id = options.id
			this.gatDatas()
		},
		onShow() {
		},
		methods: {
			//获取数据
			gatDatas(id) {
				gainPasswd(id).then(res => {
					console.log(res, "数据")
					if (res.statusCode == 200) {
						this.passData = res.data
					}

				})
			},

			//提交修改数据
			submit(e) {
				const params = {
					Origin: "app",
					RequestData: {
						oldpwd: this.form1.password,
						newpwd: this.form1.password1
					},
				}
				console.log(params.RequestData.newpwd, "得到值")
				// return
				if (this.form1.password && this.form1.password1) {
					if (this.form1.password1 != this.form1.password2) {
						this.$.toast('两次密码不一致')
					} else {
						SubmitPassword(JSON.stringify(params)).then(res => {
							if (res.statusCode == 200) {
								this.$.toast("修改成功")
								setTimeout(() => {
									uni.navigateBack({
										delta: 1
									})
								}, 1000)
							} else {
								this.$.toast('原密码不正确？')
							}
						})
					}
				} else {
					this.$.toast('请填写完整信息')
				}
			},
			niex() {
				this.is_type = true
			}
		}
	};
</script>

<style lang="scss" scoped>
	@import "index.scss";
</style>

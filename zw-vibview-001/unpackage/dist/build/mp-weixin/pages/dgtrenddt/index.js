(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/dgtrenddt/index"],{"33bf":function(t,n,e){"use strict";var a=e("bfda"),i=e.n(a);i.a},"40f4":function(t,n,e){"use strict";e.d(n,"b",(function(){return i})),e.d(n,"c",(function(){return o})),e.d(n,"a",(function(){return a}));var a={qiunDataCharts:function(){return Promise.all([e.e("common/vendor"),e.e("uni_modules/qiun-data-charts/components/qiun-data-charts/qiun-data-charts")]).then(e.bind(null,"ea88"))},uRadioGroup:function(){return Promise.all([e.e("common/vendor"),e.e("components/u-radio-group/u-radio-group")]).then(e.bind(null,"5dcb"))},uRadio:function(){return e.e("components/u-radio/u-radio").then(e.bind(null,"7b81"))},uPicker:function(){return Promise.all([e.e("common/vendor"),e.e("components/u-picker/u-picker")]).then(e.bind(null,"dec30"))}},i=function(){var t=this,n=t.$createElement;t._self._c},o=[]},"445f":function(t,n,e){"use strict";(function(t){e("aa96"),e("5c17");a(e("66fd"));var n=a(e("c4d8"));function a(t){return t&&t.__esModule?t:{default:t}}wx.__webpack_require_UNI_MP_PLUGIN__=e,t(n.default)}).call(this,e("543d")["createPage"])},"5b95":function(t,n,e){"use strict";(function(t){Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var a=o(e("a34a")),i=e("70eb");function o(t){return t&&t.__esModule?t:{default:t}}function r(t,n,e,a,i,o,r){try{var s=t[o](r),u=s.value}catch(c){return void e(c)}s.done?n(u):Promise.resolve(u).then(a,i)}function s(t){return function(){var n=this,e=arguments;return new Promise((function(a,i){var o=t.apply(n,e);function s(t){r(o,a,i,s,u,"next",t)}function u(t){r(o,a,i,s,u,"throw",t)}s(void 0)}))}}function u(t,n,e){return n in t?Object.defineProperty(t,n,{value:e,enumerable:!0,configurable:!0,writable:!0}):t[n]=e,t}var c={data:function(){return{units:"",unitsed:"",signals:"",datayAxis:[],dataxAxis:[],params:{year:!0,month:!0,day:!0,hour:!0,minute:!0,second:!1},show:!1,radioList:[{name:"加速度",disabled:!1},{name:"速度",disabled:!1},{name:"加速度",disabled:!1},{name:"温度",disabled:!1}],value:"",codeed:"",stime:"",etime:"",currentTime:"",chartData:{},opts:{rotate:!1,rotateLock:!1,enableScroll:!0,enableMarkLine:!0,color:["#1890FF"],dataPointShape:!1,dataLabel:!1,padding:[28,35,10,70],legend:{show:!1},xAxis:u({splitNumber:2,gridEval:1,rotateLabel:!0,rotateAngle:1e-4,fontSize:10,type:"grid",gridType:"dash",labelCount:5,disableGrid:!0,boundaryGap:"justify",itemCount:5e5,scrollAlign:"bottom",scrollBackgroundColor:"#262b41",scrollColor:"#262b41",dashLength:4},"scrollAlign","left"),yAxis:{fontSize:12,gridType:"dash",dashLength:1,tofix:2},extra:{line:{type:"straight",width:1}}}}},onLoad:function(t){var n=this;return s(a.default.mark((function e(){var i,o,r;return a.default.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return n.stime=n.change_date(3),n.etime=n.change_date(0),i=JSON.parse(t.data),n.data=i,n.pointname=n.data.pointname,e.next=7,n.getitemsList(n.data.pointcode);case 7:console.log(n.value,"单选"),o=n.radioList.find((function(t){return t.name===n.value})),r=o.code,n.signals=o.signal,n.gatDatalist(r);case 12:case"end":return e.stop()}}),e)})))()},onShow:function(){var n=this;setTimeout((function(){""==n.datayAxis&&t.showLoading({title:"加载中"})}),800)},onUnload:function(){},onReady:function(){this.getServerData()},methods:{numFilter:function(t){var n=parseFloat(t).toFixed(2);return n},change_date:function(t){var n=new Date;n.setDate(n.getDate()+1-1*t);var e=n.getFullYear(),a=n.getMonth()+1,i=n.getDate(),o=n.getHours(),r=n.getMinutes(),s=n.getSeconds();return o<10&&(o="0"+o),r<10&&(r="0"+r),s<10&&(s="0"+s),a<10&&(a="0"+a),i<10&&(i="0"+i),e+"-"+a+"-"+i+" "+o+":"+r+":"+s},query:function(){this.gatDatalist(this.pointId)},openDatePicker:function(t){this.show=!this.show,this.currentTime=t},confirm:function(t){var n=t.year,e=t.month,a=t.day,i=t.hour,o=t.minute;this[this.currentTime]="".concat(n,"/").concat(e,"/").concat(a," ").concat(i,":").concat(o)},radioChange:function(t,n){this.signals=n;this.gatDatalist(t);console.log(this.signals,999)},radioGroupChange:function(t){},getitemsList:function(t){var n=this;return s(a.default.mark((function e(){var o,r;return a.default.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,(0,i.gatHeritems)(t);case 2:o=e.sent,console.log(o,"单选动态数据"),200==o.statusCode&&(n.radioList=o.data,r={name:"温度",signal:100,code:n.radioList[0].code,unit:"℃"},n.radioList.push(r),n.value=o.data[1].name,console.log(n.value,"选择"));case 5:case"end":return e.stop()}}),e)})))()},gatDatalist:function(t){var n=this;console.log(this.signals,"signal");var e={Origin:"app",RequestData:{pointId:t,signalType:this.signals,startTime:this.stime,endTime:this.etime}};(0,i.gatcurveDatalist)(JSON.stringify(e)).then((function(t){console.log(1,t),200==t.statusCode&&(n.unitsed=t.data.engineerUnit,n.units=t.data.engineerUnit,100==n.signals&&(n.units="℃"),n.datayAxis=t.data.data.map((function(t){return t.rms.toFixed(3)})),n.dataxAxis=t.data.data.map((function(t){return t.dtime})),n.pointId=t.data.pointId,n.getServerData(),console.log(2))}))},getServerData:function(){var t=this;setTimeout((function(){var n={categories:t.dataxAxis,series:[{name:"单位:"+t.unitsed+"   值",data:t.datayAxis}]};t.chartData=JSON.parse(JSON.stringify(n))}),500)},goFrequency:function(){var n={pointname:this.pointname,pointId:this.pointId,signal:this.signals,name:this.value,unit:this.unitsed};console.log(n.name,"111"),100!=n.signal?(""!=n.signal&&""!=n.name||t.showModal({title:"请勾选数据类型"}),(n.signal||n.name)&&t.navigateTo({url:"/pages/dgfrequency/index?data=".concat(JSON.stringify(n))})):t.showModal({title:"请选择振动趋势"})},back:function(){t.navigateBack({delta:1})}}};n.default=c}).call(this,e("543d")["default"])},bfda:function(t,n,e){},c4d8:function(t,n,e){"use strict";e.r(n);var a=e("40f4"),i=e("f223");for(var o in i)"default"!==o&&function(t){e.d(n,t,(function(){return i[t]}))}(o);e("33bf");var r,s=e("f0c5"),u=Object(s["a"])(i["default"],a["b"],a["c"],!1,null,"4cf80579",null,!1,a["a"],r);n["default"]=u.exports},f223:function(t,n,e){"use strict";e.r(n);var a=e("5b95"),i=e.n(a);for(var o in a)"default"!==o&&function(t){e.d(n,t,(function(){return a[t]}))}(o);n["default"]=i.a}},[["445f","common/runtime","common/vendor"]]]);
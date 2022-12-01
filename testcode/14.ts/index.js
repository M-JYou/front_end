// interface RunOptions {
// 	program: string;
// 	commandline: string[] | string | (() => string);
// }
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        if (typeof b !== "function" && b !== null)
            throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
// // commandline 是字符串
// var options: RunOptions = { program: 'test1', commandline: 'Hello' };
// console.log(options.commandline);
// // commandline 是字符串数组
// options = { program: 'test1', commandline: ['Hello', 'World'] };
// console.log(options.commandline[0]);
// console.log(options.commandline[1]);
// // commandline 是一个函数表达式
// options = {
// 	program: 'test1',
// 	commandline: () => {
// 		return '**Hello World**';
// 	},
// };
// var fn: any = options.commandline;
// console.log(fn());
// interface namelist{
// 	[index:number]:string
// }
// var list2:namelist=['google','runoob','taobao']
// console.log(list2);
// interface ages{
// 	[index:string]:number
// }
// var agelist:ages;
// // agelist['runoob']=15
// // // console.log(agelist);
// // console.log(agelist['runoob']);
// agelist[2]=1
// interface Person {
// 	age:number
// }
// interface Musician extends Person {
// 	instrument:string
// }
// var drummer = <Musician>{};
// drummer.age=27
// drummer.instrument='Drums'
// console.log('年龄：'+drummer.age)
// console.log('喜欢的乐器：'+drummer.instrument)
// class Person {
// }
// class Car {
// 	// 字段
// 	engine: string;
// 	// 构造函数
// 	constructor(engine: string) {
// 		this.engine = engine;
// 	}
// 	// 方法
// 	disp(): void {
// 		console.log('函数中显示发动机型号：' + this.engine);
// 	}
// }
// // 创建一个对象
// var obj = new Car('XXSY1');
// // 访问字段
// console.log('读取发动机型号：' + obj.engine);
// // 访问方法
// obj.disp();
// class Sharp {
// 	Area: number;
// 	constructor(a: number) {
// 		this.Area = a;
// 	}
// }
// class Circle extends Sharp {
// 	disp(): void {
// 		console.log('圆的面积:  ' + this.Area);
// 	}
// }
// var obj = new Circle(223);
// obj.disp();
// export default {};
// class Root {
// 	str:string;
// }
// class Child extends Root{}
// class Leaf  extends Child{}
// var obj =new Leaf();
// obj.str="hello"
// console.log(obj.str);
// export default{}
// class PrinterClass {
// 	doPrint():void {
// 		console.log("父类的 doPrint() 方法。")
// 	}
// }
//
// class StringPrinter extends PrinterClass {
// 	doPrint():void {
// 		super.doPrint() // 调用父类的函数
// 		console.log("子类的 doPrint()方法。")
// 	}
// }
// export default{}
// class StaticMem{
//   static num:number;
//   static disp():void{
//     console.log("num值为"+StaticMem.num);
//   }
// }
// StaticMem.num=12
// StaticMem.disp()
// export class Person{}
// var obj =new Person()
// var isPerson=obj instanceof Person;
// console.log("obj对象是Person类实例化来的吗？"+isPerson);
// class Encapsulate {
//   str1:string="hello"
//   private str2:string="world"
// }
// var obj =new Encapsulate()
// console.log(obj.str1);
// // console.log(obj.str2);
// interface ILoan{
//   // interest:number
// }
// class Agriloan implements ILoan{
//   interest: number
//   rebate:number
//   constructor(interest:number,rebate:number){
//     this.interest=interest
//     this.rebate=rebate
//   }
// }
// var obj=new Agriloan(10,1)
// console.log("利润为："+obj.interest+",抽成为："+obj.rebate);
// function math(num1:number,num2:number):number{
//   return Math.pow(num1,num2)
// };
// console.log(math(2,4));
// const math=(num1:number,num2:number):number=>{
//   return Math.exp(2)
// }
// console.log(Math.exp(2));
// function greet(name:string):void{
//   console.log('hello',name);
// }
// greet('mjy')
// function mySlice(start?:number,end?:number):void{
//   console.log('起始索引：',start,'结束索引：',end);
// }
// mySlice()
// mySlice(1)
// mySlice(1,6)
// mySlice(undefined,0)
// let Person:{
//   name:string
//   age:number
//   sayHi:()=>void
//   greet:(name:string)=>void
// }={
//   name:'MJY',
//   age:22,
//   sayHi(){},
//   greet(name){}
// }
//
// 继承
// interface point2D {x:number,y:number};
// interface point3D extends point2D {z:number}
// let mjy:point3D={
//   x:1,
//   y:11,
//   z:3
// }
// console.log(mjy);
// 元组的使用
// let position:[number,number]=[37,88]
// console.log(position);
// 类型推论
// let age=18
// console.log(age);
// function add(num1: number, num2: number) {
// 	return num1 + num2;
// }
// console.log(add(5,6))
// 字面量类型
// function changeDirection(direction:'up'|'down'|'left'|'right'):void{
// console.log(direction);
// }
// changeDirection('up')
// 枚举
// enum Direction {up,down,left,right}
// function changeDirection(direction:Direction){
// console.log(direction);
// }
// changeDirection(Direction.up);
// 字符串枚举
// enum Direction{
// 	up='UP',
// 	down='DOWN',
// 	left='LEFT',
// 	right='RIGHT'
// }
// console.log(
// 	Direction.down,
// 	Direction.left,
// 	Direction.right,
// 	Direction.up
// );
// any类型
// let obj:any={}
// obj.a={}
// obj.b=10
// obj()
// typeof
// console.log(typeof 'hello world');
// class类
// class Person {}
// const p=new Person()
//构造函数（构造函数不能指定返回值类型）
// class Person {
//   age:number
//   gender:string
//   constructor(age:number,gender:string){
//     this.age=age
//     this.gender=gender
//   }
// }
// const a=new Person(22,'未知')
// console.log(a.age,a.gender);
// class类的实例方法
// class point {
// 	x = 1;
// 	y = 2;
// 	scale(n: number) {
// 		this.x *= n;
// 		this.y *= n;
// 	}
// }
// const a= new point()
// a.scale(5)
// console.log(a.x,a.y);
// class类的继承
var Animal = /** @class */ (function () {
    function Animal() {
    }
    Animal.prototype.move = function () {
        console.log('moving along!');
    };
    return Animal;
}());
var Dog = /** @class */ (function (_super) {
    __extends(Dog, _super);
    function Dog() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    Dog.prototype.bark = function () {
        console.log('汪');
    };
    return Dog;
}(Animal));
var dog = new Dog();
dog.bark();
dog.move();
console.log(dog.bark, dog.move);

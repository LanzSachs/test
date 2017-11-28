4.1根据Arguments对象下标获取函数参数
求函数参数的 和







function f() {
     sum=0;
     for(i=0;i<arguments.length;i++){
         sum +=arguments[i];
     }
     console.log(sum);
 }
 f(1,2,3);
 f(1,5,6,9);

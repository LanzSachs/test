<?php
//封装一个手机号加密函数
// 15312345678  =》 153****5678
function encrypt_phone($phone){
	return substr($phone, 0, 3) . '****' . substr($phone, 7, 4);
}
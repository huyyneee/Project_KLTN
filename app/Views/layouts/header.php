

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= isset($title) ? $title : 'App' ?></title>
	<link rel="stylesheet" href="/assets/css/output.css">
	<style>
		/* Dropdown hover for Tailwind JIT */
		.group:hover .group-hover\:block { display: block; }
	</style>
</head>
<body class="bg-white">
	   <!-- Header image banner -->
	   <div class="header-banner w-full bg-[#c2e3ce] flex flex-col items-center mt-16">
		   <img src="/assets/images/header.png" alt="Header Banner" class="w-full object-cover" />
	   </div>
	   <header class="fixed top-0 left-0 w-full z-50 bg-green-800 text-white shadow header-main">
			   <div class="max-w-7xl mx-auto flex items-center px-4 h-16">
			   <!-- Left: Logo + Danh mục -->
			   <div class="flex items-center flex-shrink-0 min-w-max space-x-2">
				   <img src="/assets/images/logo.png" alt="Logo" class="h-12 w-12 object-contain bg-white rounded-full shadow" />
				   <div class="relative group">
					   <button class="flex items-center px-3 py-2 focus:outline-none">
						   <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
						   <span class="font-semibold tracking-wide">DANH MỤC</span>
					   </button>
					   <div class="absolute left-0 mt-2 w-64 bg-white text-green-900 rounded shadow-lg border border-green-200 group-hover:block hidden transition-all duration-200 min-w-max z-50">
						   <a href="#" class="flex items-center justify-between px-4 py-2 hover:bg-green-100">
							   Chăm Sóc Da Mặt
							   <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
						   </a>
						   <a href="#" class="flex items-center justify-between px-4 py-2 hover:bg-green-100">
							   Trang Điểm
							   <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
						   </a>
						   <a href="#" class="flex items-center justify-between px-4 py-2 hover:bg-green-100">
							   Chăm Sóc Tóc Và Da Đầu
							   <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
						   </a>
						   <a href="#" class="flex items-center justify-between px-4 py-2 hover:bg-green-100">
							   Chăm Sóc Cơ Thể
							   <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
						   </a>
						   <a href="#" class="flex items-center justify-between px-4 py-2 hover:bg-green-100">
							   Chăm Sóc Cá Nhân
							   <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
						   </a>
						   <a href="#" class="flex items-center justify-between px-4 py-2 hover:bg-green-100">
							   Nước Hoa
							   <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
						   </a>
						   <a href="#" class="flex items-center justify-between px-4 py-2 hover:bg-green-100">
							   Thực Phẩm Chức Năng
							   <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
						   </a>
					   </div>
				   </div>
				   <!-- Search box -->
				   <form action="#" method="get" class="ml-2 flex items-center bg-white rounded">
					   <input type="text" name="q" placeholder="Tìm sản phẩm..." class="px-3 py-1 rounded-l outline-none text-green-900 w-44 text-sm" />
					   <button type="submit" class="px-2">
						   <svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
					   </button>
				   </form>
			   </div>
			   <!-- Center: Menu -->
			   <div class="flex-1 flex justify-center">
				   <nav class="menu flex space-x-6">
					   <a href="/" class="font-semibold hover:text-yellow-300">TRANG CHỦ</a>
					   <a href="/about" class="font-semibold hover:text-yellow-300">GIỚI THIỆU</a>
					   <a href="#" class="font-semibold hover:text-yellow-300">TIN TỨC & SỰ KIỆN</a>
					   <a href="/contact" class="font-semibold hover:text-yellow-300">LIÊN HỆ</a>
				   </nav>
			   </div>
			   <!-- Right: Icons -->
			   <div class="flex items-center space-x-2 flex-shrink-0 min-w-max">
				   <a href="#" class="bg-white rounded w-10 h-10 flex items-center justify-center mr-2">
					   <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4"/><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/></svg>
				   </a>
				   <a href="#" class="bg-green-700 rounded-full w-10 h-10 flex items-center justify-center">
					   <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 8-4 8-4s8 0 8 4"/></svg>
				   </a>
			   </div>
		   </div>
	</header>
	<div class="h-14"></div>



<?php



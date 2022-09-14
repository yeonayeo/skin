<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require VENDORS.'/autoload.php';

// 이용권 다운로드
if (!function_exists('downloadTicket')) {
	function downloadTicket($list, $count) {
		$objPHPExcel = new PHPExcel();

		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A1", "총 ".$count."건")
		-> setCellValue("B1", "다운로드일: ".CURR_DATE2)
		-> setCellValue("A3", "이용권명")
		-> setCellValue("B3", "종류")
		-> setCellValue("C3", "비고");

		$count = 1;
		foreach($list as $key => $val) {
			$num = 4 + $key;
			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue(sprintf("A%s", $num), $val['name'])
			-> setCellValue(sprintf("B%s", $num), $val['kind'])
			-> setCellValue(sprintf("C%s", $num), $val['note']);
			$count++;

			// 비활성화 배경색 변경
			if($val['is_use']=='N') {
				$objPHPExcel -> getActiveSheet() -> getStyle("A".$num.":C".$num."") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("F9F3EE");
				$objPHPExcel -> getActiveSheet() -> getStyle("A".$num.":C".$num."") -> getFont() -> getColor() -> setRGB("BEB9B9");
			}
		}

		// 가로 넓이 조정
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(40);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(60);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(100);

		// 전체 가운데 정렬
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A3:C3", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// 타이틀 부분
		$objPHPExcel -> getActiveSheet() -> getStyle("A3:C3") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A3:C3") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A3:C3") -> getFont() -> getColor() -> setRGB("FFFFFF");

		// 시트 네임
		$objPHPExcel -> getActiveSheet() -> setTitle("이용권");

		// 첫번째 시트(Sheet)로 열리게 설정
		$objPHPExcel -> setActiveSheetIndex(0);

		// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
		$filename = iconv("UTF-8", "EUC-KR", "이용권_".CURR_DATE_STRING);

		// 브라우저로 엑셀파일을 리다이렉션
		header("Content-Type:application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=".$filename.".xls");
		header("Cache-Control:max-age=0");

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter -> save("php://output");
	}
}

// 화장품 다운로드
if (!function_exists('downloadCosmetic')) {
	function downloadCosmetic($list, $count) {
		$objPHPExcel = new PHPExcel();

		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A1", "총 ".$count."건")
		-> setCellValue("B1", "다운로드일: ".CURR_DATE2)
		-> setCellValue("A3", "제품명")
		-> setCellValue("B3", "재고")
		-> setCellValue("C3", "판매가")
		-> setCellValue("D3", "메모");

		$count = 1;
		foreach($list as $key => $val) {
			$num = 4 + $key;
			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue(sprintf("A%s", $num), $val['name'])
			-> setCellValue(sprintf("B%s", $num), $val['remain_quantity'])
			-> setCellValue(sprintf("C%s", $num), $val['sales_price'])
			-> setCellValue(sprintf("D%s", $num), $val['memo']);
			$count++;

			// 비활성화 배경색 변경
			if($val['is_use']=='N') {
				$objPHPExcel -> getActiveSheet() -> getStyle("A".$num.":D".$num."") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("F9F3EE");
				$objPHPExcel -> getActiveSheet() -> getStyle("A".$num.":D".$num."") -> getFont() -> getColor() -> setRGB("BEB9B9");
			}
		}

		// 가로 넓이 조정
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(35);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(90);

		// 전체 가운데 정렬
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A3:D3", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// 타이틀 부분
		$objPHPExcel -> getActiveSheet() -> getStyle("A3:D3") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A3:D3") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A3:D3") -> getFont() -> getColor() -> setRGB("FFFFFF");

		// 시트 네임
		$objPHPExcel -> getActiveSheet() -> setTitle("화장품");

		// 첫번째 시트(Sheet)로 열리게 설정
		$objPHPExcel -> setActiveSheetIndex(0);

		// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
		$filename = iconv("UTF-8", "EUC-KR", "화장품_".CURR_DATE_STRING);

		// 브라우저로 엑셀파일을 리다이렉션
		header("Content-Type:application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=".$filename.".xls");
		header("Cache-Control:max-age=0");

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter -> save("php://output");
	}
}

// 화장품 재고 다운로드
if (!function_exists('downloadCosmeticStock')) {
	function downloadCosmeticStock($list, $count, $info, $is_super, $start_date='', $end_date='') {
		if($start_date=='null') $start_date='';
		if($end_date=='null') $end_date='';

		$objPHPExcel = new PHPExcel();

		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A1", "제품명")
		-> setCellValue("B1", $info['name'])
		-> setCellValue("A2", "판매가")
		-> setCellValue("B2", number_format($info['sales_price'])."원");
		if($is_super) {
			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue("A3", "입고가")
			-> setCellValue("B3", number_format($info['purchase_price'])."원")
			-> setCellValue("A4", "메모")
			-> setCellValue("B4", $info['memo']);
		} else {
			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue("A3", "메모")
			-> setCellValue("B3", $info['memo']);
		}
		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A6", "총 ".$count."건");
		if($start_date || $end_date) {
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("B6", "기간: ".$start_date." ~ ".$end_date);
		}

		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A7", "날짜")
		-> setCellValue("B7", "구분")
		-> setCellValue("C7", "수량")
		-> setCellValue("D7", "담당자")
		-> setCellValue("E7", "재고")
		-> setCellValue("F7", "비고");

		$count = 1;
		foreach($list as $key => $val) {
			$num = 8 + $key;
			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue(sprintf("A%s", $num), $val['goods_date'])
			-> setCellValue(sprintf("B%s", $num), $val['type'])
			-> setCellValue(sprintf("C%s", $num), $val['quantity'])
			-> setCellValue(sprintf("D%s", $num), $val['manager_name'])
			-> setCellValue(sprintf("E%s", $num), $val['remain_quantity'])
			-> setCellValue(sprintf("F%s", $num), $val['note']);
			$count++;
		}

		// 가로 넓이 조정
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(10);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(15);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("E") -> setWidth(10);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("F") -> setWidth(90);

		// 전체 가운데 정렬
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A7:F7", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// 타이틀 부분
		$objPHPExcel -> getActiveSheet() -> getStyle("A1") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A1") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A2") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A2") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A3") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A3") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A7:F7") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A7:F7") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A7:F7") -> getFont() -> getColor() -> setRGB("FFFFFF");
		if($is_super) {
			$objPHPExcel -> getActiveSheet() -> getStyle("A4") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
			$objPHPExcel -> getActiveSheet() -> getStyle("A4") -> getFont() -> getColor() -> setRGB("FFFFFF");
		}

		// 시트 네임
		$objPHPExcel -> getActiveSheet() -> setTitle($info['name']);

		// 첫번째 시트(Sheet)로 열리게 설정
		$objPHPExcel -> setActiveSheetIndex(0);

		// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
		$filename = iconv("UTF-8", "EUC-KR", "화장품_".$info['name']."_".CURR_DATE_STRING);

		// 브라우저로 엑셀파일을 리다이렉션
		header("Content-Type:application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=".$filename.".xls");
		header("Cache-Control:max-age=0");

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter -> save("php://output");
	}
}

// 비품 다운로드
if (!function_exists('downloadStuff')) {
	function downloadStuff($list, $count) {
		$objPHPExcel = new PHPExcel();

		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A1", "총 ".$count."건")
		-> setCellValue("B1", "다운로드일: ".CURR_DATE2)
		-> setCellValue("A3", "비품명")
		-> setCellValue("B3", "재고")
		-> setCellValue("C3", "구분")
		-> setCellValue("D3", "메모");

		$count = 1;
		foreach($list as $key => $val) {
			$num = 4 + $key;
			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue(sprintf("A%s", $num), $val['name'])
			-> setCellValue(sprintf("B%s", $num), $val['remain_quantity'])
			-> setCellValue(sprintf("C%s", $num), $val['type'])
			-> setCellValue(sprintf("D%s", $num), $val['memo']);
			$count++;
		}

		// 가로 넓이 조정
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(35);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(90);

		// 전체 가운데 정렬
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A3:D3", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// 타이틀 부분
		$objPHPExcel -> getActiveSheet() -> getStyle("A3:D3") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A3:D3") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A3:D3") -> getFont() -> getColor() -> setRGB("FFFFFF");

		// 시트 네임
		$objPHPExcel -> getActiveSheet() -> setTitle("비품");

		// 첫번째 시트(Sheet)로 열리게 설정
		$objPHPExcel -> setActiveSheetIndex(0);

		// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
		$filename = iconv("UTF-8", "EUC-KR", "비품_".CURR_DATE_STRING);

		// 브라우저로 엑셀파일을 리다이렉션
		header("Content-Type:application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=".$filename.".xls");
		header("Cache-Control:max-age=0");

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter -> save("php://output");
	}
}

// 비품 재고 다운로드
if (!function_exists('downloadStuffStock')) {
	function downloadStuffStock($list, $count, $info, $is_super, $start_date='', $end_date='') {
		if($start_date=='null') $start_date='';
		if($end_date=='null') $end_date='';

		$objPHPExcel = new PHPExcel();

		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A1", "비품명")
		-> setCellValue("B1", $info['name'])
		-> setCellValue("A2", "입고가")
		-> setCellValue("B2", number_format($info['purchase_price'])."원")
		-> setCellValue("A3", "메모")
		-> setCellValue("B3", $info['memo'])
		-> setCellValue("A6", "총 ".$count."건");
		if($start_date || $end_date) {
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue("B6", "기간: ".$start_date." ~ ".$end_date);
		}

		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A7", "날짜")
		-> setCellValue("B7", "구분")
		-> setCellValue("C7", "수량")
		-> setCellValue("D7", "담당자")
		-> setCellValue("E7", "재고")
		-> setCellValue("F7", "비고");

		$count = 1;
		foreach($list as $key => $val) {
			$num = 8 + $key;
			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue(sprintf("A%s", $num), $val['goods_date'])
			-> setCellValue(sprintf("B%s", $num), $val['type'])
			-> setCellValue(sprintf("C%s", $num), $val['quantity'])
			-> setCellValue(sprintf("D%s", $num), $val['manager_name'])
			-> setCellValue(sprintf("E%s", $num), $val['remain_quantity'])
			-> setCellValue(sprintf("F%s", $num), $val['note']);
			$count++;
		}

		// 가로 넓이 조정
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(10);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(15);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("E") -> setWidth(10);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("F") -> setWidth(90);

		// 전체 가운데 정렬
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A7:F7", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// 타이틀 부분
		$objPHPExcel -> getActiveSheet() -> getStyle("A1") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A1") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A2") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A2") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A3") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A3") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A7:F7") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A7:F7") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A7:F7") -> getFont() -> getColor() -> setRGB("FFFFFF");
		if($is_super) {
			$objPHPExcel -> getActiveSheet() -> getStyle("A4") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
			$objPHPExcel -> getActiveSheet() -> getStyle("A4") -> getFont() -> getColor() -> setRGB("FFFFFF");
		}

		// 시트 네임
		$objPHPExcel -> getActiveSheet() -> setTitle($info['name']);

		// 첫번째 시트(Sheet)로 열리게 설정
		$objPHPExcel -> setActiveSheetIndex(0);

		// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
		$filename = iconv("UTF-8", "EUC-KR", "비품_".$info['name']."_".CURR_DATE_STRING);

		// 브라우저로 엑셀파일을 리다이렉션
		header("Content-Type:application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=".$filename.".xls");
		header("Cache-Control:max-age=0");

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter -> save("php://output");
	}
}

// 판매내역 다운로드
if (!function_exists('downloadSales')) {
	function downloadSales($list, $sum, $total_sum, $start_date='', $end_date='') {
		if($start_date=='null') $start_date='';
		if($end_date=='null') $end_date='';

		$objPHPExcel = new PHPExcel();
		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A1", "기간: ".$start_date." ~ ".$end_date)
		-> setCellValue("A2", "카드 결제")
		-> setCellValue("A3", "현금 결제")
		-> setCellValue("A4", "합계")
		-> setCellValue("A6", "기간 내 총 매출 합계")
		-> setCellValue("B1", "이용권 매출")
		-> setCellValue("B2", $sum['card'][0]."원")
		-> setCellValue("B3", $sum['money'][0]."원")
		-> setCellValue("B4", $sum['sum'][0]."원")
		-> setCellValue("B6", $total_sum."원")
		-> setCellValue("C1", "화장품 매출")
		-> setCellValue("C2", $sum['card'][1]."원")
		-> setCellValue("C3", $sum['money'][1]."원")
		-> setCellValue("C4", $sum['sum'][1]."원")
		-> setCellValue("D1", "직접 입력 매출")
		-> setCellValue("D2", $sum['card'][2]."원")
		-> setCellValue("D3", $sum['money'][2]."원")
		-> setCellValue("D4", $sum['sum'][2]."원")
		-> setCellValue("E1", "추가 관리 매출")
		-> setCellValue("E2", $sum['card'][3]."원")
		-> setCellValue("E3", $sum['money'][3]."원")
		-> setCellValue("E4", $sum['sum'][3]."원")
		-> setCellValue("A7", "날짜")
		-> setCellValue("B7", "구분")
		-> setCellValue("C7", "결제방식")
		-> setCellValue("D7", "항목명")
		-> setCellValue("E7", "금액");

		$count = 1;
		foreach($list as $key => $val) {
			$num = 8 + $key;
			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue(sprintf("A%s", $num), $val['sales_date'])
			-> setCellValue(sprintf("B%s", $num), $val['type'])
			-> setCellValue(sprintf("C%s", $num), $val['payment_method'])
			-> setCellValue(sprintf("D%s", $num), $val['name'])
			-> setCellValue(sprintf("E%s", $num), $val['amount']);
			$count++;
		}

		// 가로 넓이 조정
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("E") -> setWidth(30);

		// 전체 가운데 정렬
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:E1", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A7:E7", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A2", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A3", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A4", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// 타이틀 부분
		$objPHPExcel -> getActiveSheet() -> getStyle("A1") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A2") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A2") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A3") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A3") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A4") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A4") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A1:E1") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A1:E1") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A7:E7") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A7:E7") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A7:E7") -> getFont() -> getColor() -> setRGB("FFFFFF");

		// 시트 네임
		$objPHPExcel -> getActiveSheet() -> setTitle("판매내역");

		// 첫번째 시트(Sheet)로 열리게 설정
		$objPHPExcel -> setActiveSheetIndex(0);

		// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
		$filename = iconv("UTF-8", "EUC-KR", "판매내역정산_".CURR_DATE_STRING);

		// 브라우저로 엑셀파일을 리다이렉션
		header("Content-Type:application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=".$filename.".xls");
		header("Cache-Control:max-age=0");

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter -> save("php://output");
	}
}

// 관리내역 다운로드
if (!function_exists('downloadAdmin')) {
	function downloadAdmin($list, $count, $total_amount, $start_date='', $end_date='') {
		if($start_date=='null') $start_date='';
		if($end_date=='null') $end_date='';

		$objPHPExcel = new PHPExcel();
		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A1", "기간: ".$start_date." ~ ".$end_date)
		-> setCellValue("A2", "건 수")
		-> setCellValue("B1", "이용권 사용")
		-> setCellValue("B2", $count[0]."건")
		-> setCellValue("B4", $total_amount."원")
		-> setCellValue("C1", "직접 입력")
		-> setCellValue("C2", $count[1]."건")
		-> setCellValue("D1", "추가 관리")
		-> setCellValue("D2", $count[2]."건")
		-> setCellValue("A4", "기간 내 총 매출 합계")
		-> setCellValue("A5", "날짜")
		-> setCellValue("B5", "구분")
		-> setCellValue("C5", "결제방식")
		-> setCellValue("D5", "항목명")
		-> setCellValue("E5", "금액");

		$count = 1;
		foreach($list as $key => $val) {
			$num = 6 + $key;
			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue(sprintf("A%s", $num), $val['admin_date'])
			-> setCellValue(sprintf("B%s", $num), $val['type'])
			-> setCellValue(sprintf("C%s", $num), $val['payment_method'])
			-> setCellValue(sprintf("D%s", $num), $val['name'])
			-> setCellValue(sprintf("E%s", $num), $val['amount']);
			$count++;
		}

		// 가로 넓이 조정
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(30);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("E") -> setWidth(30);

		// 전체 가운데 정렬
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:D1", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A2", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A5:E5", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// 타이틀 부분
		$objPHPExcel -> getActiveSheet() -> getStyle("A1") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A2") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A2") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A1:D1") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A1:D1") -> getFont() -> getColor() -> setRGB("FFFFFF");
		$objPHPExcel -> getActiveSheet() -> getStyle("A5:E5") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A5:E5") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("C38370");
		$objPHPExcel -> getActiveSheet() -> getStyle("A5:E5") -> getFont() -> getColor() -> setRGB("FFFFFF");

		// 시트 네임
		$objPHPExcel -> getActiveSheet() -> setTitle("관리내역");

		// 첫번째 시트(Sheet)로 열리게 설정
		$objPHPExcel -> setActiveSheetIndex(0);

		// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
		$filename = iconv("UTF-8", "EUC-KR", "관리내역정산_".CURR_DATE_STRING);

		// 브라우저로 엑셀파일을 리다이렉션
		header("Content-Type:application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=".$filename.".xls");
		header("Cache-Control:max-age=0");

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter -> save("php://output");
	}
}

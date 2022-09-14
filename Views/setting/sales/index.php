<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container" v-cloak>
    <div class="page_head mb60">
      <h2 class="page_title">설정 및 관리</h2>
      <h3 class="page_subtitle">매출 현황</h3>
    </div>
    <div class="page_contents">
      <div class="noti_area">
        ※ 매출 현황은 시스템에 기록된 정보에 근거하여 단순 계산된 자료로, <span class="c_red">실제 매출과 상이할 수 있습니다.</span>
      </div>
      <div class="tab_area">
        <ul class="page_contents">
          <li id="sales_detail" class="tab_menu">
            <a href="#sales_detail">판매내역 정산</a>
            <div class="tab_content">
              <div class="content_head">
                <div class="label">기간 선택</div>
                <div class="date_box">
                  <input type="text" id="kronos1" style="width: 180px;" v-model="sales_start_date" ref="sales_start_date">
                  <span>~</span>
                  <input type="text" id="kronos2" style="width: 180px;" v-model="sales_end_date" ref="sales_end_date">
                  <button type="button" class="btn c4 s" @click="get_sales">기간 검색</button> <!-- 0622 기간검색 버튼 추가 -->
                </div>
                <div style="margin-left: auto;"> <!-- 0719작업 -->
                  <button type="button" class="btn_download" @click="excel_download('excel_download_sales')"></button>
                </div>
              </div>
              <div class="content_body">
                <div class="table_list">
                  <table class="default">
                    <colgroup>
                      <col style="width: 220px;">
                      <col style="width: 320px;">
                      <col style="width: 200px;">
                      <col style="width: 420px;">
                      <col style="width: auto;">
                    </colgroup>
                    <thead>
                      <tr>
                        <th>날짜</th>
                        <th>구분</th>
                        <th>결제방식</th>
                        <th>항목명</th>
                        <th>금액</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="sales_list.length" v-for="sales in sales_list">
                        <td>{{ sales.sales_date }}</td>
                        <td>{{ sales.type }}</td>
                        <td>{{ sales.payment_method }}</td>
                        <td>{{ sales.name }}</td>
                        <td>{{ sales.amount }}원</td>
                      </tr>
                      <!-- 결과없음 -->
                      <tr class="list_empty" v-if="!sales_list.length">
                        <td colspan="5">검색 결과가 없습니다.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="content_foot"> <!-- 0719작업 -->
                <table class="sales_table" style="max-width: 820px;">
                  <colgroup>
                    <col style="width: auto;">
                    <col style="width: 180px;">
                    <col style="width: 180px;">
                    <col style="width: 180px;">
                    <col style="width: 180px;">
                  </colgroup>
                  <thead>
                    <tr>
                      <th><!-- 빈 태그 --></th>
                      <th>이용권 매출</th>
                      <th>화장품 매출</th>
                      <th>직접 입력 매출</th>
                      <th>추가관리 매출</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="ta_l">카드 결제</td>
                      <td class="ta_r" v-for="card in card_list">{{ card }}원</td>
                    </tr>
                    <tr>
                      <td class="ta_l">현금 결제</td>
                      <td class="ta_r" v-for="money in money_list">{{ money }}원</td>
                    </tr>
                    <tr>
                      <td class="ta_l">합계</td>
                      <td class="ta_r" v-for="sum in sum_list">{{ sum }}원</td>
                    </tr>
                  </tbody>
                </table>
                <div class="total mt20">
                  <div class="box">
                    <div class="label">기간 내 총 매출 합계</div>
                    <div class="amount">{{ total_sales_amount }} 원</div>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li id="care_detail" class="tab_menu">
            <a href="#care_detail">관리내역 정산</a>
            <div class="tab_content">
              <div class="content_head">
                <div class="label">기간 선택</div>
                <div class="date_box">
                  <input type="text" id="kronos3" style="width: 180px;" v-model="admin_start_date" ref="admin_start_date">
                  <span>~</span>
                  <input type="text" id="kronos4" style="width: 180px;" v-model="admin_end_date" ref="admin_end_date">
                  <button type="button" class="btn c4 s" @click="get_admin">기간 검색</button> <!-- 0622 기간검색 버튼 추가 -->
                </div>
                <div style="margin-left: auto;"> <!-- 0719작업 -->
                  <button type="button" class="btn_download" @click="excel_download('excel_download_admin')"></button>
                </div>
              </div>
              <div class="content_body">
                <div class="table_list">
                  <table class="default">
                    <colgroup>
                      <col style="width: 220px;">
                      <col style="width: 320px;">
                      <col style="width: 200px;">
                      <col style="width: 420px;">
                      <col style="width: auto;">
                    </colgroup>
                    <thead>
                      <tr>
                        <th>날짜</th>
                        <th>구분</th>
                        <th>결제방식</th>
                        <th>항목명</th>
                        <th>금액</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="admin_list.length" v-for="admin in admin_list">
                        <td>{{ admin.admin_date }}</td>
                        <td>{{ admin.type }}</td>
                        <td>{{ admin.payment_method }}</td>
                        <td>{{ admin.name }}</td>
                        <td>{{ admin.amount }}원</td>
                      </tr>
                      <!-- 결과없음 -->
                      <tr class="list_empty" v-if="!admin_list.length">
                        <td colspan="5">검색 결과가 없습니다.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="content_foot"> <!-- 0719작업 -->
                <table class="sales_table" style="max-width: 630px;">
                  <colgroup>
                    <col style="width: auto;">
                    <col style="width: 180px;">
                    <col style="width: 180px;">
                    <col style="width: 180px;">
                  </colgroup>
                  <thead>
                    <tr>
                      <th><!-- 빈 태그 --></th>
                      <th>이용권 사용</th>
                      <th>직접 입력</th>
                      <th>추가관리</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="ta_l">건 수</td>
                      <td class="ta_r">{{ ticket_use_cnt }}건</td>
                      <td class="ta_r">{{ direct_cnt }}건</td>
                      <td class="ta_r">{{ add_admin_cnt }}건</td>
                    </tr>
                  </tbody>
                </table>
                <div class="total mt20">
                  <div class="box">
                    <div class="label">기간 내 총 매출 합계</div>
                    <div class="amount">{{ total_admin_amount }} 원</div>
                  </div>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </main>

  <script>
    location.href = "#sales_detail";

    var FRONT = Vue.createApp({
      data() {
        return {
          res: RES,
          req: {},
          err: {},

          sales_start_date: RES.start_date,
          sales_end_date: RES.end_date,
          admin_start_date: RES.start_date,
          admin_end_date: RES.end_date,
          sales_list: RES.sales_list,
          total_sales_amount: RES.total_sales_amount,
          admin_list: RES.admin_list,
          total_admin_amount: RES.total_admin_amount,
          /*0723*/
          money_list: RES.money_list,
          card_list: RES.card_list,
          sum_list: RES.sum_list,
          ticket_use_cnt: RES.ticket_use_cnt,
          direct_cnt: RES.direct_cnt,
          add_admin_cnt: RES.add_admin_cnt,
        }
      },
      mounted() {
        $('#kronos1').kronos({
          period: {
            to: '#kronos2'
          },
          onChange: date => {
            this.sales_start_date = this.$refs.sales_start_date.value;
          }
        });
        $('#kronos2').kronos({
          period: {
            from: '#kronos1'
          },
          onChange: date => {
            this.sales_end_date = this.$refs.sales_end_date.value;
          }
        });
        $('#kronos3').kronos({
          period: {
            to: '#kronos4'
          },
          onChange: date => {
            this.admin_start_date = this.$refs.admin_start_date.value;
          }
        });
        $('#kronos4').kronos({
          period: {
            from: '#kronos3'
          },
          onChange: date => {
            this.admin_end_date = this.$refs.admin_end_date.value;
          }
        });
      },
      methods: {
        get_sales() {
          $.ajax({
            url: '/setting/sales/get_sales',
            data: {start_date: this.sales_start_date, end_date: this.sales_end_date},
            success: (res) => {
              if (res.res_cd === 'OK') {
                this.sales_list = res.data.sales_list;
                this.money_list = res.data.money_list;
                this.card_list = res.data.card_list;
                this.sum_list = res.data.sum_list;
                this.total_sales_amount = res.data.total_sales_amount;
              } else {
                alert(res.err_msg);
              }
            }
          });
        },
        get_admin() {
          $.ajax({
            url: '/setting/sales/get_admin',
            data: {start_date: this.admin_start_date, end_date: this.admin_end_date},
            success: (res) => {
              if (res.res_cd === 'OK') {
                this.admin_list = res.data.admin_list;
                this.total_admin_amount = res.data.total_admin_amount;
                this.ticket_use_cnt = res.data.ticket_use_cnt;
                this.direct_cnt = res.data.direct_cnt;
                this.add_admin_cnt = res.data.add_admin_cnt;
              } else {
                alert(res.err_msg);
              }
            }
          });
        },
        excel_download(type) {
          let start_date = '';
          let end_date = '';
          if(type=='excel_download_sales') {
            start_date = this.sales_start_date;
            end_date = this.sales_end_date;
          } else {
            start_date = this.admin_start_date;
            end_date = this.admin_end_date;
          }
          window.open("/setting/sales/"+type+"?start_date="+start_date+"&end_date="+end_date, "_blank");
        }
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>

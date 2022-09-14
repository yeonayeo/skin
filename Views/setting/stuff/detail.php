<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container" v-cloak>
    <div class="page_head mb60">
      <h2 class="page_title">설정 및 관리</h2>
      <h3 class="page_subtitle">비품 관리</h3>
    </div>
    <div class="page_contents">
      <div class="contents_head">
        <button type="button" class="btn_page_back" @click="action_back"></button>
        <div class="title">{{ name }}</div>
        <button type="button" class="btn e2 s" @click="action_delete">비품 삭제</button>
      </div>
      <div class="contents_body">
        <section class="left_area">
          <div class="info_box" style="width: 500px;">
            <div class="box_head">
              <div class="box_title">비품 정보</div>
              <button type="button" class="btn_update_book" @click="popup_update()">비품 정보 수정</button>
            </div>
            <div class="box_content">
              <div class="box_table_list">
                <table>
                  <tbody>
                    <tr>
                      <th>구분</th>
                      <td>{{ type }}</td>
                    </tr>
                    <tr> <!-- 0719작업 -->
                      <th>입고가</th>
                      <td>{{ purchase_price }}원</td>
                    </tr>
                    <tr>
                      <th style="vertical-align: top;">메모</th>
                      <td>{{ memo }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </section>
        <section class="right_area">
          <div class="info_box" style="width: 1100px; max-height: unset; min-height: 425px;">
            <div class="box_head mb20">
              <div class="box_title">재고 관리</div>
              <button type="button" class="btn_change" @click="popup_change_regist()">변경내역 추가</button>
            </div>
            <div class="box_content">
              <div class="content_head">
                <div class="label">기간 선택</div>
                <div class="date_box">
                  <input type="text" id="kronos1" style="width: 180px;" v-model="start_date" ref="start_date">
                  <span>~</span>
                  <input type="text" id="kronos2" style="width: 180px;" v-model="end_date" ref="end_date">
                  <button class="btn c4 s" @click="action_search">기간 검색</button>
                </div>
                <div style="margin-left: auto;"> <!-- 0719작업 -->
                  <button type="button" class="btn_download" @click="excel_download"></button>
                </div>
              </div>
              <div class="content_body" v-if="stock_list.length>0"> <!-- 테이블+페이지네이션 영역 묶음 -->
                <div class="box_table_list2 default">
                  <table style="width: 100%;">
                    <colgroup>
                      <col style="width: 160px;">
                      <col style="width: 160px;">
                      <col style="width: 160px;">
                      <col style="width: 160px;">
                      <col style="width: 160px;">
                      <col style="width: auto;">
                    </colgroup>
                    <thead>
                      <tr>
                        <th>날짜</th>
                        <th>구분</th>
                        <th>수량</th>
                        <th>담당자</th>
                        <th>재고</th>
                        <th>비고</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="stock in stock_list">
                        <td>{{ stock.goods_date }}</td>
                        <td>{{ stock.type }}</td>
                        <td>{{ stock.quantity }}</td>
                        <td>{{ stock.manager_name }}</td>
                        <td>{{ stock.remain_quantity }}</td>
                        <td>{{ stock.note }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="pagination" v-if="pagination.page_range.length">
                  <div class="navi">
                    <button type="button" @click="pagination_page(pagination.prev_page)"><i class="material-icons">navigate_before</i></button>
                  </div>
                  <div class="pages">
                    <button type="button" @click="pagination_page(page)" :class="{on: pagination.page == page}" v-for="page in pagination.page_range">{{ page }}</button>

                  </div>
                  <div class="navi">
                    <button type="button" @click="pagination_page(pagination.next_page)"><i class="material-icons">navigate_next</i></button>
                  </div>
                </div>
              </div>
              <!-- 결과없음 -->
              <div class="empty_cosmetic" v-if="stock_list.length<=0">
                <div class="text">재고 관리 내역이 없습니다.</div>
              </div>
              <!-- //결과없음 -->
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>

  <script>
    var FRONT = Vue.createApp({
      data() {
        return {
          res: RES,
          get: GET,
          req: {},
          err: {},
          id: RES.id,
          name: RES.name,
          type: RES.type,
          purchase_price: RES.purchase_price,
          memo: RES.memo,
          stock_list: RES.stock_list,
          start_date: RES.start_date,
          end_date: RES.end_date,
          pagination: RES.pagination
        }
      },
      mounted() {
        $('#kronos1').kronos({
          period: {
            to: '#kronos2'
          },
          onChange: date => {
            this.start_date = this.$refs.start_date.value;
          }
        });
        $('#kronos2').kronos({
          period: {
            from: '#kronos1'
          },
          onChange: date => {
            this.end_date = this.$refs.end_date.value;
          }
        });
      },
      methods: {
        popup_update() {
          popup_update = sunrise({
            data: {},
            target: '/setting/stuff/popup_update?id='+this.id
          })
        },
        popup_change_regist() {
          popup_change_regist = sunrise({
            data: {},
            target: '/setting/stuff/popup_change_regist?id='+this.id
          })
        },
        action_back() {
          history.back();
        },
        action_search() {
          CORE.set_url_parameter({page: 1, start_date: this.start_date, end_date: this.end_date});
        },
        pagination_page(page) {
          CORE.set_url_parameter({page: page, start_date: this.start_date, end_date: this.end_date});
        },
        action_delete() {
          if(!confirm('비품 삭제시 복구할 수 없습니다.\n정말 삭제하시겠습니까?')) return;
          $.ajax({
            url: '/setting/stuff/action_delete',
            data: {id: [this.id]},
            success: (res) => {
              if (res.res_cd === 'OK') {
                location.href = `/setting/stuff`;
              } else {
                alert(res.err_msg);
              }
            }
          });
        },
        excel_download() {
          window.open("/setting/stuff/excel_download_stock?stuff_id="+this.id+"&start_date="+this.start_date+"&end_date="+this.end_date, "_blank");
        }
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>

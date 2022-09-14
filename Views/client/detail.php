<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container" v-cloak>
    <h2 class="page_title">고객 정보</h2>
    <div class="page_contents">
      <div class="contents_head">
        <button type="button" class="btn_page_back" @click="action_back"></button>
        <div class="title">{{ basic_info.name }}<span>고객님</span></div>
        <button type="button" class="btn e2 s" @click="action_delete">고객 삭제</button>
      </div>
      <div class="contents_body">
        <section class="left_area">
          <div class="info_box">
            <div class="box_head">
              <div class="box_title">기본 정보</div>
              <button type="button" class="btn_update_book" @click="popup_update_basic()">기본 정보 수정</button>
            </div>
            <div class="box_content">
              <div class="box_table_list">
                <table>
                  <tbody>
                    <tr>
                      <th>고객명</th>
                      <td>{{ basic_info.name }}</td>
                      <th>주소</th>
                      <td>{{ basic_info.address }}</td>
                    </tr>
                    <tr>
                      <th>연락처</th>
                      <td>
                        <div class="sms">
                          {{ basic_info.hp }}
                          <a href="/setting/sms" class="btn_sms"></a>
                        </div>
                      </td>
                      <th rowspan="2">메모</th>
                      <td rowspan="2">{{ basic_info.memo }}</td>
                    </tr>
                    <tr>
                      <th>생년월일</th>
                      <td>{{ basic_info.birth }}</td>
                    </tr>
                    <tr>
                      <th>성별</th>
                      <td>{{ basic_info.gender }}</td>
                      <th>특이사항</th>
                      <td>{{ basic_info.special_note }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="info_box mt20">
            <div class="box_head">
              <div class="box_title">방문 정보</div>
            </div>
            <div class="box_content">
              <div class="box_table_list" v-if="is_visit">
                <table>
                  <tbody>
                    <tr>
                      <th>첫 방문일</th>
                      <td>{{ visit_info.first_visit_date }}</td>
                      <th>최근 방문일</th>
                      <td>{{ visit_info.last_visit_date }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="box_table_list2" v-if="is_visit">
                <table>
                  <colgroup>
                    <col style="width: 160px;">
                    <col style="width: 160px;">
                    <col style="width: 180px;">
                    <col style="width: auto;">
                  </colgroup>
                  <thead>
                    <tr>
                      <th>날짜</th>
                      <th>구분</th>
                      <th>관리명</th>
                      <th>특이사항</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="visit in visit_info.list" @click="popup_detail_visit(visit.schedule_id)">
                      <td>{{ visit.booking_date }}</td>
                      <td>{{ visit.status }}</td>
                      <td>{{ visit.manage_name }}</td>
                      <td>{{ visit.special_note }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- 0621 페이지네이션 추가 -->
            <div class="pagination" v-if="visit_info.list.length && visit_pagination.page_range.length">
              <div class="navi">
                <button type="button" @click="pagination_page(visit_pagination.prev_page)"><i class="material-icons">navigate_before</i></button>
              </div>
              <div class="pages">
                <button type="button" @click="pagination_page(page)" :class="{on: visit_pagination.page == page}" v-for="page in visit_pagination.page_range">{{ page }}</button>

              </div>
              <div class="navi">
                <button type="button" @click="pagination_page(visit_pagination.next_page)"><i class="material-icons">navigate_next</i></button>
              </div>
            </div>
            <!-- 결과없음 -->
            <div class="empty"  v-if="!is_visit">
              <div class="text">방문 정보가 없습니다.</div>
            </div>
          </div>
        </section>
        <section class="right_area">
          <div class="info_box">
            <div class="box_head">
              <div class="box_title">이용권 정보</div>
              <button type="button" class="btn_ticket" @click="popup_regist_ticket()">이용권 추가</button>
            </div>
            <div class="box_content" v-if="is_ticket" v-for="(ticket, index) in ticket_list">
              <div class="toggle_area" :class="{visible: ticket.is_toggle}">
                <div class="toggle_head" @click="ticket.is_toggle = !ticket.is_toggle">
                  <div class="row">
                    <div class="row_title">
                      <div class="date">{{ticket.purchase_date}}</div>
                    </div>
                    <div class="row_content">
                      <div class="title">{{ticket.name}} <span class="desc1">(잔여 {{ticket.remain}})</span>
                      <span class="desc2" v-if="ticket.is_complete">(사용 완료)</span>
                    </div>
                    </div>
                    <div class="btn_more">상세 정보<i class="material-icons">keyboard_arrow_down</i></div>
                  </div>
                </div>
                <div class="toggle_body">
                  <div class="row"> <!-- 0719작업 -->
                    <div class="row_title">구매금액</div>
                    <div class="row_content">
                      <div class="tag c1 l mr10">{{ ticket.payment_method }}</div>
                      <div class="title">{{ ticket.amount }}원 <span class="desc1" v-if="ticket.discount_rate">({{ ticket.discount_rate }}% 할인 적용)</span></div>
                    </div>
                  </div>
                  <div class="row mt15">
                    <div class="row_title">
                      <button type="button" class="btn_memo" @click="memo_update(index)">메모</button>
                    </div>
                    <div class="row_content">
                      <div class="text" v-if="!ticket.is_memo_update">{{ticket.memo}}</div>
                      <!-- 메모 수정 -->
                      <input type="text" placeholder="메모를 입력해주세요" style="width: 370px;" v-if="ticket.is_memo_update" v-model="ticket.update_memo">
                      <button type="button" class="btn e3 s" v-if="ticket.is_memo_update" @click="action_update_ticket_memo(ticket.id, ticket.update_memo, index)">메모 저장</button>
                      <!-- END -->
                    </div>
                  </div>
                  <div class="row mt10">
                    <div class="box_table_list2 default">
                      <table>
                        <colgroup>
                          <col style="width: 120px;">
                          <col style="width: auto;">
                          <col style="width: 160px;">
                        </colgroup>
                        <thead>
                          <tr>
                            <th>날짜</th>
                            <th>비고</th>
                            <th>잔여</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(use, idx) in ticket.use_list">
                            <td>{{use.use_date}}</td>
                            <td>{{use.note}}</td>
                            <td>{{use.remain}}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="row mt5">
                    <button type="button" class="btn_used_ticket" v-if="!ticket.is_complete" @click="action_ticket_confirm(ticket.id, index)">사용 완료 처리</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- 결과없음 -->
            <div class="empty" v-if="!is_ticket">
              <div class="text">이용권 정보가 없습니다.</div>
            </div>
          </div>
          <div class="info_box">
            <div class="box_head">
              <div class="box_title">제품 구매 내역</div>
              <button type="button" class="btn_cosmetic" @click="popup_cosmetic_regist()">구매 내역 추가</button>
            </div>
            <div class="box_content" v-if="is_cosmetic" v-for="(cosmetic, index) in cosmetic_list">
              <div class="toggle_area" :class="{visible: cosmetic.is_toggle}">
                <div class="toggle_head" @click="cosmetic.is_toggle = !cosmetic.is_toggle">
                  <div class="row">
                    <div class="row_title">
                      <div class="date">{{ cosmetic.purchase_date }}</div>
                    </div>
                    <div class="row_content">
                      <div class="title">{{ cosmetic.name }}</div>
                    </div>
                    <div class="btn_more">상세 내역<i class="material-icons">keyboard_arrow_down</i></div>
                  </div>
                </div>
                <div class="toggle_body">
                  <div class="row">
                    <div class="row_title">결제금액</div>
                    <div class="row_content">
                      <div class="text">{{ cosmetic.price }}원</div>
                    </div>
                  </div>
                  <div class="row mt15">
                    <div class="row_title">구매 수량</div>
                    <div class="row_content">
                      <div class="text">{{ cosmetic.quantity }}개</div>
                    </div>
                  </div>
                  <div class="row mt15">
                    <div class="row_title">판매자</div>
                    <div class="row_content">
                      <div class="text">{{ cosmetic.manager_name }}</div>
                    </div>
                  </div>
                  <div class="row mt15"> <!-- 0719작업 -->
                    <div class="row_title">구매금액</div>
                    <div class="row_content">
                      <div class="tag c1 l mr10">{{ cosmetic.payment_method }}</div>
                      <div class="title">{{ cosmetic.amount }}원 <span class="desc1" v-if="cosmetic.discount_rate">({{ cosmetic.discount_rate }}% 할인 적용)</span></div>
                    </div>
                  </div>
                  <div class="row mt15">
                    <div class="row_title">메모</div>
                    <div class="row_content">
                      <div class="text">{{ cosmetic.memo }}</div>
                    </div>
                  </div>
                  <div class="row mt20">
                    <button type="button" class="btn_cosmetic_update" v-if="is_super" @click="popup_cosmetic_update(cosmetic.id)">상세 내역 수정</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- 결과없음 -->
            <div class="empty" v-if="!is_cosmetic">
              <div class="text">제품 구매 내역이 없습니다.</div>
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

          toggle_visible2: false,

          id: RES.id,
          basic_info: RES.basic_info,
          visit_info: RES.visit_info,
          visit_pagination : RES.visit_pagination,
          ticket_list: RES.ticket_list,
          cosmetic_list: RES.cosmetic_list,
          is_visit: RES.is_visit,
          is_ticket: RES.is_ticket,
          is_cosmetic: RES.is_cosmetic,
          is_super: RES.is_super,
        }
      },
      mounted() {},
      methods: {
        popup_update_basic() {
          popup_update_basic = sunrise({
            data: {},
            target: '/client/popup_update_basic?id='+this.res.id
          })
        },
        popup_regist_ticket() {
          popup_regist_ticket = sunrise({
            data: {},
            target: '/client/popup_regist_ticket?id='+this.id
          })
        },
        popup_detail_visit(schedule_id) {
          popup_detail_visit = sunrise({
            data: {},
            target: '/client/popup_detail_visit?id='+schedule_id
          })
        },
        popup_cosmetic_regist() {
          popup_cosmetic_regist = sunrise({
            data: {},
            target: '/client/popup_cosmetic_regist?id='+this.id
          })
        },
        popup_cosmetic_update(id) {
          popup_cosmetic_update = sunrise({
            data: {},
            target: '/client/popup_cosmetic_update?id='+id
          })
        },
        pagination_page(page) {
          $.ajax({
            url: '/client/get_visit_list',
            data: {page: page, id: this.id},
            success: (res) => {
              if (res.res_cd === 'OK') {
                this.visit_pagination = res.data.pagination;
                this.visit_info.list = res.data.list;
              } else {
                console.log(res);
              }
            }
          });
        },
        action_back() {
          history.back();
        },
        memo_update(index) {
          this.ticket_list[index].is_memo_update = true;
        },
        action_update_ticket_memo(ticket_id, memo, index) {
          $.ajax({
            url: '/client/action_update_ticket_memo',
            data: {ticket_id: ticket_id, memo: memo},
            success: (res) => {
              if (res.res_cd === 'OK') {
                this.ticket_list[index].memo = res.data.memo;
                this.ticket_list[index].is_memo_update = false;
              } else {
                console.log(res);
              }
            }
          });
        },
        action_ticket_confirm(ticket_id) {
          $.ajax({
            url: '/client/action_ticket_confirm',
            data: {ticket_id: ticket_id},
            success: (res) => {
              if (res.res_cd === 'OK') {
                location.href = `/client/detail?id=`+this.id;
              } else {
                alert(res.err_msg);
              }
            }
          });
        },
        action_delete() {
          if(!confirm('고객 정보 삭제시 복구할 수 없습니다.\n정말 삭제하시겠습니까?')) return;
          $.ajax({
            url: '/client/action_delete',
            data: {ids: [this.id]},
            success: (res) => {
              if (res.res_cd === 'OK') {
                alert('삭제가 완료되었습니다.');
                location.href = `/client`;
              } else {
                console.log(res);
              }
            }
          });
        }
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>

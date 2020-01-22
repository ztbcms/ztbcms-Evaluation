<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <h3>评价</h3>
            <div class="filter-container">
                <el-select v-model="listQuery.target_type" filterable placeholder="类型" @change="search">
                    <el-option label="全部" value=""></el-option>
                    <el-option label="美食店铺" value="shop_id"></el-option>
                    <el-option label="门票产品" value="product_id"></el-option>
                </el-select>
                <el-select v-model="listQuery.review_status" filterable placeholder="审核状态" @change="search">
                    <el-option label="全部" value=""></el-option>
                    <el-option label="待审核" value="0"></el-option>
                    <el-option label="审核通过" value="1"></el-option>
                    <el-option label="审核不通过" value="2"></el-option>
                </el-select>
                <el-button class="filter-item" type="primary" style="margin-left: 10px;"
                           @click="search">
                    筛选
                </el-button>
                <el-button type="primary" @click="toEdit()">添加</el-button>

            </div>
            <el-table
                :data="list"
                border
                fit
                highlight-current-row
                style="width: 100%;"
            >
                <el-table-column label="评论对象" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.target_info.name }}（ID:{{ scope.row.target }}）</span>
                    </template>
                </el-table-column>
                <el-table-column label="评价内容" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.content }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="评分" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.rate }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="用户" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.user_nickname }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="图片" align="center">
                    <template slot-scope="scope">
                        <template v-for="item in scope.row.images">
                            <el-image
                                style="width: 100px; height: 100px"
                                :src="item"
                                :preview-src-list="[item]"
                                lazy>
                            </el-image>
                        </template>
                    </template>
                </el-table-column>
                <el-table-column label="发布日期" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.create_time_date }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="展示" align="center">
                    <template slot-scope="{row}">
                        <el-switch
                                @change="changeShowStatus(row)"
                                v-model="row.show_status"
                                active-value="1"
                                inactive-value="0">
                        </el-switch>
                    </template>
                </el-table-column>

                <el-table-column label="审核状态" align="center">
                    <template slot-scope="scope">
<!--                        审核状态 0待审核 1审核通过 2审核不通过-->

                        <el-button v-if="scope.row.review_status == 0" type="primary" size="mini" @click="toDoReview(scope.row.id,1)">
                            通过
                        </el-button>
                        <el-button v-if="scope.row.review_status == 0" type="danger" size="mini" @click="toDoReview(scope.row.id,2)" style="margin: 4px">
                            不通过
                        </el-button>
                        <span v-if="scope.row.review_status == 1">通过</span>
                        <span v-if="scope.row.review_status == 2">不通过</span>
                    </template>
                </el-table-column>

                <el-table-column label="操作" align="center" width="230" class-name="small-padding fixed-width">
                    <template slot-scope="scope">
                        <el-button type="danger" size="mini" @click="toDelete(scope.row)" style="margin: 4px">
                            删除
                        </el-button>

                    </template>
                </el-table-column>

            </el-table>

            <div class="pagination-container">
                <el-pagination
                    background
                    layout="prev, pager, next, jumper"
                    :total="total"
                    v-show="total>0"
                    :current-page.sync="listQuery.page"
                    :page-size.sync="listQuery.limit"
                    @current-change="getList"
                >
                </el-pagination>
            </div>

        </el-card>
    </div>

    <style>
        .filter-container {
            padding-bottom: 10px;
        }

        .pagination-container {
            padding: 32px 16px;
        }
    </style>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    dialogFormVisible: false,
                    form: {
                        id: '',
                        name: '',
                    },
                    list: [],
                    total: 0,
                    listQuery: {
                        page: 1,
                        limit: 10,
                        title: '',
                        review_status: '',
                        target_type: ''
                    },
                },
                watch: {},
                filters: {},
                methods: {
                    search: function () {
                        this.listQuery.page = 1;
                        this.getList();
                    },
                    getList: function () {
                        var that = this;
                        $.ajax({
                            url: "/Evaluation/EvaluationManage/getList",
                            type: "post",
                            dataType: "json",
                            data: that.listQuery,
                            success: function(res){
                                if(res.status){
                                    that.list = res.data.items
                                    that.total = res.data.total_items
                                }else{
                                    layer.msg(res.msg)
                                }
                            }
                        });
                    },
                    doDelete: function (id) {
                        var that = this;
                        $.ajax({
                            url: "/Evaluation/EvaluationManage/doDelete",
                            type: "post",
                            dataType: "json",
                            data: {id: id},
                            success: function(res){
                                layer.msg(res.msg)
                                if(res.status){
                                    that.getList()
                                }
                            }
                        });
                    },
                    toDelete: function (item) {
                        var that = this;
                        layer.confirm('是否确定删除该项内容吗？', {
                            btn: ['确认', '取消'] //按钮
                        }, function () {
                            that.doDelete(item.id)
                            layer.closeAll();
                        }, function () {
                            layer.closeAll();
                        });
                    },

                    toDoReview: function(id, status){
                        var that = this;
                        let msg = '';
                        if(status == 1){
                            msg = '是否确定通过审核？'
                        } else{
                            msg = '是否确定不通过审核？'
                        }
                        layer.confirm(msg, {
                            btn: ['确认', '取消'] //按钮
                        }, function () {
                            that.doReview(id, status)
                            layer.closeAll();
                        }, function () {
                            layer.closeAll();
                        });
                    },
                    doReview: function(id, status){
                        var that = this;
                        $.ajax({
                            url: "/Evaluation/EvaluationManage/doEdit",
                            type: "post",
                            dataType: "json",
                            data: {id: id, review_status : status },
                            success: function(res){
                                layer.msg(res.msg)
                                if(res.status){
                                    that.getList()
                                }
                            }
                        });
                    },

                },
                mounted: function () {
                    this.getList();
                },

            })
        })
    </script>
</block>
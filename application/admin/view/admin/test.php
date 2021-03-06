{include file="template/_meta" /}
<title></title>
</head>

{block name="css"}
<link rel="stylesheet" type="text/css" href="__STATIC__/admin/common/css/aaa.css" />
<style type="text/css">
    body {
        background: #fff;
    }

    .ziji {
        width: auto;
    }

    .ziji>input {
        width: 400px;
    }

    .layui-btn {
        border-radius: 6px;
        height: 50px;
    }

    .layui-form-lists .layui-btn {
        width: 170px;
    }

    .layui-btn-normal {
        margin-right: 150px;
    }

    .layui-form-item {
        margin-left: 200px;
        margin-top: 80px;
    }

    .seach_laber1 {
        font-size: 13px;
        width: 100px;
        text-align: center;
        color: #333;
    }

    .layui-textarea {
        width: 100%;
    }

    .tips {
        margin-top: 5px;
    }
    .on {
        color: #21A5FA !important;
        border-bottom: solid 3px #21A5FA;
    }

    .distribution_list {
        margin-bottom: 10px;
        border-bottom: solid 1px #D7D7D7;
    }

    .distribution_list ul {
        display: flex;
        align-items: center;
    }

    .distribution_list a {
        font-size: 14px;
        padding: 9px;
        color: #5E5E5E;
    }
</style>

{/block}

<body>
    {block name="content"}
    <form class="layui-form layui-form-lists" action="{:url('admin/Admin/admin_goods_tactics')}" method="post" enctype="multipart/form-data">
        {include file="template/_top"/}
        <div class="form-item ">
            <input type="hidden" name="one0[key]" value="goods_limit">
            <input type="hidden" name="one0[id]" value="{$data1['id']}">
            <label class="form-label " style="width:20%">甩品限制</label>
            <div class="form-input ziji">
                <input type="text" name="one0[limit_num]" value="{$data1['value']['limit_num']}" lay-verify="title" autocomplete="off" placeholder="    请输入个数"
                    class="layui-input">
                <div class="tips">
                    <span class="red">*</span>每个用户同时开甩的商品上限，多规格也算，<span class="red">-1</span>表示不限
                </div>
            </div>
        </div>
        <div class="form-item ">
                <input type="hidden" name="one1[key]" value="goods_limit_own">
                <input type="hidden" name="one1[id]" value="{$data2['id']}">
            <label class="form-label " style="width:20%">自己甩数限制</label>
            <div class="form-input ziji">
                <input type="text" name="one1[person_up_num]" value="{$data2['value']['person_up_num']}" lay-verify="title" autocomplete="off" placeholder="    请输入次数"
                    class="layui-input">
                <div class="tips">
                    <span class="red">*</span>每个用户每天单个商品的甩次上限，<span class="red">-1</span>表示不限
                </div>
            </div>
        </div>
        <div class="form-item ">
            <label class="form-label " style="width:20%"></label>
            <div class="form-input ziji">
                <input type="text" name="one1[own_num]" value="{$data2['value']['person_up_num']}" lay-verify="title" autocomplete="off" placeholder="    请输入次数"
                    class="layui-input">
                <div class="tips">
                    <span class="red">*</span>每个用户每天自己甩的总甩数上限，<span class="red">-1</span>表示不限
                </div>
            </div>
        </div>
        <div class="form-item ">
                <input type="hidden" name="one2[key]" value="help_goods_limit">
                <input type="hidden" name="one2[id]" value="{$data3['id']}">
            <label class="form-label " style="width:20%">帮甩数限制</label>
            <div class="form-input ziji">
                <input type="text" name="one2[goods_limit_num]" value="{$data3['value']['goods_limit_num']}" lay-verify="title" autocomplete="off" placeholder="    请输入次数"
                    class="layui-input">
                <div class="tips">
                    <span class="red">*</span>每个用户每天对单个商品的帮甩次数，<span class="red">-1</span>表示不限
                </div>
            </div>
        </div>
        <div class="form-item ">
            <label class="form-label " style="width:20%"></label>
            <div class="form-input ziji">
                <input type="text" name="one2['own_help_num']" value="{$data3['value']['own_help_num']}"  lay-verify="title" autocomplete="off" placeholder="    请输入次数"
                    class="layui-input">
                <div class="tips">
                    <span class="red">*</span>每个用户每天总的帮甩次数，<span class="red">-1</span>表示不限
                </div>
            </div>
        </div>
        <div class="layui-form-item" style="width:100%;">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" type="submit" id="submit">保存</button>
                <button type="button" class="layui-btn layui-btn-primary"
                    onClick="javascript :history.back(-1);">返回</button>
            </div>

        </div>
    </form>
    {/block}
    <!--_footer 作为公共模版分离出去-->
    {include file="template/_footer" /}
    <!--/_footer 作为公共模版分离出去-->

    <!--请在下方写此页面业务相关的脚本-->
    {block name="bottom"}
    <script type="text/javascript">
        layui.config({
            base: "js/"
        }).use(['form', 'layer', 'jquery', 'laypage'], () => {
            var form = layui.form(),
                layer = parent.layer === undefined ? layui.layer : parent.layer,
                laypage = layui.laypage,
                $ = layui.jquery;
        })



    </script>

    {/block}

</body>

</html>
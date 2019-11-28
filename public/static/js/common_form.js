//通用from ajax表单
$(function  () {
    layui.use(['laydate','form'], function(){
        var form = layui.form;

        // 监听全选
        form.on('checkbox(checkall)', function(data){
            if(data.elem.checked){
                $('tbody input').prop('checked',true);
            }else{
                $('tbody input').prop('checked',false);
            }
            form.render('checkbox');
        });

        //自定义验证规则
        form.verify({
            text:function(val){
                if(val == ""){
                    return "请完整填写表单";
                }
            },
            pass: [/(.+){6,12}$/, '密码必须6到12位!'],
            repass: function(value) {
                if ($('#L_pass').val() != $('#L_repass').val()) {
                    return '两次密码不一致';
                }
            }
        });
        //监听提交
        form.on('submit(sub)', function(data){
          $.ajax({
              url:data.form.action,
              method:"POST",
              data:data.field,
              beforeSend:function(){
                  layer.load();
              },
              success:function (data) {
                  layer.closeAll();
                  console.log(data);
                  if(data.code == 200){
                      //success
                      alertCommon(data,1);
                  }else{
                      //fail
                      alertCommon(data,2);
                  }
                  var index =0;
                  try {
                       index = parent.layer.getFrameIndex(window.name);
                  } catch (error) {}
                  //关闭layer弹窗
                if(index > 0){
                    xadmin.father_reload();
                }
              },
              error:function () {
                  layer.closeAll();
                  layer.msg("提交错误");
              }
          });
            return false;
        });
    });
})

/**
 * 删除
 * @param obj
 * @param id
 */
function member_del(obj,id){
    layer.confirm('确认要删除吗？',function(index){
        //发异步删除数据
        ajaxDel(id);
    });
}

/**
 * 批量删除
 * @param argument
 */
function delAll (argument) {
    var ids = [];
    // 获取选中的id
    $('tbody input').each(function(index, el) {
        if($(this).prop('checked')){
            ids.push($(this).val())
        }
    });
    layer.confirm('确认要删除吗？'+ids.toString(),function(index){
        //捉到所有被选中的，发异步进行删除
         ajaxDel(ids);
    });
}

/**
 * 删除-ajax
 * @param id
 * @return boolean
 */
function ajaxDel(id){
    $.ajax({
        url:"del",
        data:{id:id},
        type:"POST",
        beforeSend:function(){
            layer.load();
        },
        success:function(data){
            console.log(data);
            layer.closeAll();
            if(data.code == 200){
                //success
                layer.msg("删除成功",{
                    icon:1,
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                },function(){
                    window.location.reload();
                });
            }else{
                //fail
                layer.msg('删除失败', {icon: 2});

            }
        }
    })
}



/**
 * 通用弹窗
 * @param data
 * @param icon
 */
function alertCommon(data,icon){
    layer.msg(data.msg,{
        icon:icon,
        time: 2000 //2秒关闭（如果不配置，默认是3秒）
    },function(){
        if(data.url != ""){
            window.location.href=data.url;
        }
    });
}

/**
 * 导出到excel
 * @param $uri
 */
function outputExcel(uri){
    $.ajax({
        type:"GET",
        url:uri,
        beforeSend:function(){
            layer.load();
        },
        success:function(data){
            layer.closeAll();
            layer.msg(data.msg);
        }
    })
}
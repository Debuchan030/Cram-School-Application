// 公佈欄模板
var bulletin_template = ({ bulletin_id, bulletin_title, bulletin_content, bulletin_time }) => `
<tr data-bs-toggle="collapse" data-bs-target="#${bulletin_id}" aria-expanded="false"
aria-controls="content">
    <td><textarea cols="30" rows="1">${bulletin_title}</textarea></td>
</tr>
<tr class="collapse" id="${bulletin_id}">
    <td>
        <textarea name="" id="" cols="30" rows="3"
            class="m-2">${bulletin_content}</textarea>
        <textarea name="" id="" cols="30" rows="1"
            class="m-2">${bulletin_time}</textarea>
            <button class="m-2 bulletin_save">儲存</button>
            <button class="m-2 bulletin_delete">刪除</button></td>
    </tr>
`;

// 獲取所有公佈欄資訊 id title content time , buxiban_bulletin.length

for (var i = 0; i < 5 /*buxiban.length*/; i++) {
    var id = "buxiban_id" + i //buxiban_bulletin[i].buxiban_id
    var title = "test" + i //buxiban_bulletin[i].buxiban_title
    var content = "test" + i//buxiban_bulletin[i].buxiban_content
    var time = "2021/08/23"//buxiban_bulletin[i].buxiban_time
    $('#bulletin_board').append([
        { bulletin_id: id, bulletin_title: title, bulletin_content: content, bulletin_time: time },
    ].map(bulletin_template));
}
// console.log("hihi")

// 修改貼文
$(".bulletin_save").on("click",bulletin_save_func)
function bulletin_save_func(){
    console.log("save")
}
//刪除貼文
$(".bulletin_delete").on("click",bulletin_delete_func)
function bulletin_delete_func(){
    console.log("delete")
}
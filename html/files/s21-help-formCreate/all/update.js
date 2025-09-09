
function slabel(form)
{
    
    lvalue =  $(document.getElementsByName('lvalue')[0]).val()
    lsize = $(document.getElementsByName('lsize')[0]).val()
    console.log(lvalue, lsize)
    id = form.attr('sid')
    
    div = document.getElementById(id)
    
    
    div.firstChild.remove()
    $('#'+id).prepend(`<`+lsize+`><label for="e01">`+lvalue+`</label></`+lsize+`>`)
    
}

function stext(form){
    eid = $(document.getElementsByName('id')[0]).val()
    lvalue = $(document.getElementsByName('lvalue')[0]).val()
    lsize = $(document.getElementsByName('lsize')[0]).val()
    tname = $(document.getElementsByName('tname')[0]).val()
    
    id = form.attr('sid')
    div = document.getElementById(id)
    div.firstChild.nextSibling.setAttribute('id',eid)
    div.firstChild.nextSibling.setAttribute('name',tname)
    div.firstChild.remove()
    $('#'+id).prepend(`<`+lsize+`><label for="`+eid+`">`+lvalue+`</label></`+lsize+`>`)
   
    
    
}

function sradio(form,num){
    arr = ['id','lvalue','rname','id1','rvalue1','lvalue1','id2','rvalue2','lvalue2']
    values={}
    if(num==2)
    {
        for(i=0;i<arr.length;i++)
        {
            values[arr[i]] = $(document.getElementsByName(arr[i])[0]).val()
            console.log(values[arr[i]])
        }

        id = form.attr('sid')
        console.log(id)
        ele = document.getElementById(id)
        ele.firstChild.firstChild.setAttribute('for',values['id'])
        
        $(ele.firstChild.firstChild).text(values['lvalue'])
        rb1 = ele.firstChild.nextSibling;
        rb1.setAttribute('name',values['rname'])
        rb1.setAttribute('id',values['id1'])
        rb1.setAttribute('value',values['rvalue1'])
        $(rb1.nextSibling).text(values['lvalue1'])
        rb2 = rb1.nextSibling.nextSibling.nextSibling;
        rb2.setAttribute('id',values['id2'])
        rb2.setAttribute('value',values['rvalue2'])
        $(rb2.nextSibling).text(values['lvalue2'])
    }
    else if(num==3){
        arr.push('id3')
        arr.push('rvalue3')
        arr.push('lvalue3')
        for(i=0;i<arr.length;i++)
        {
            values[arr[i]] = $(document.getElementsByName(arr[i])[0]).val()
        }
        console.log(values['id3'],values['rvalue3'],values['lvalue3'])
        id = form.attr('sid')
    
        ele = document.getElementById(id)
        ele.firstChild.firstChild.getAttribute('for',values['id'])
        $(ele.firstChild.firstChild).text(values['lvalue'])
        rb1 = ele.firstChild.nextSibling;
        rb1.setAttribute('name',values['rname'])
        rb1.setAttribute('id',values['id1'])
        rb1.setAttribute('value',values['rvalue1'])
        $(rb1.nextSibling).text(values['lvalue1'])
        rb2 = rb1.nextSibling.nextSibling.nextSibling;
        rb2.setAttribute('id',values['id2'])
        rb2.setAttribute('value',values['rvalue2'])
        $(rb2.nextSibling).text(values['lvalue2'])

        rb3 = rb2.nextSibling.nextSibling.nextSibling;
        rb3.setAttribute('id',values['id3'])
        rb3.setAttribute('value',values['rvalue3'])
        $(rb3.nextSibling).text(values['lvalue3'])
    }
    else if(num==4){
        a = ['id3','rvalue3','lvalue3','id4','rvalue4','lvalue4']
        for(i=0;i<a.length;i++)
        {
           arr.push(a[i])
        }
        for(i=0;i<arr.length;i++)
        {
            values[arr[i]] = $(document.getElementsByName(arr[i])[0]).val()
        }

        id = form.attr('sid')
    
        ele = document.getElementById(id)
        ele.firstChild.firstChild.getAttribute('for',values['id'])
        $(ele.firstChild.firstChild).text(values['lvalue'])
        rb1 = ele.firstChild.nextSibling;
        rb1.setAttribute('name',values['rname'])
        rb1.setAttribute('id',values['id1'])
        rb1.setAttribute('value',values['rvalue1'])
        $(rb1.nextSibling).text(values['lvalue1'])
        rb2 = rb1.nextSibling.nextSibling.nextSibling;
        rb2.setAttribute('id',values['id2'])
        rb2.setAttribute('value',values['rvalue2'])
        $(rb2.nextSibling).text(values['lvalue2'])

        rb3 = rb2.nextSibling.nextSibling.nextSibling;
        rb3.setAttribute('id',values['id3'])
        rb3.setAttribute('value',values['rvalue3'])
        $(rb3.nextSibling).text(values['lvalue3'])

        rb4 = rb3.nextSibling.nextSibling.nextSibling;
        rb4.setAttribute('id',values['id4'])
        rb4.setAttribute('value',values['rvalue4'])
        $(rb4.nextSibling).text(values['lvalue4'])

    }
}

function scheckbox(form,num){
    arr = ['id','lvalue','cname','id1','cvalue1','lvalue1','id2','cvalue2','lvalue2']
    values=[]
    
    if(num==2)
    {
        for(i=0;i<arr.length;i++)
        {
            values[arr[i]] = $(document.getElementsByName(arr[i])[0]).val()
        }
        id = form.attr('sid')
    
        ele = document.getElementById(id)
        ele.firstChild.firstChild.setAttribute('for',values['id'])
        
        $(ele.firstChild.firstChild).text(values['lvalue'])
        cb1 = ele.firstChild.nextSibling;
        cb1.setAttribute('name',values['cname'])
        cb1.setAttribute('id',values['id1'])
        cb1.setAttribute('value',values['cvalue1'])
        $(cb1.nextSibling).text(values['lvalue1'])
        cb2 = cb1.nextSibling.nextSibling.nextSibling.nextSibling;
        cb2.setAttribute('id',values['id2'])
        cb2.setAttribute('value',values['cvalue2'])
        $(cb2.nextSibling).text(values['lvalue2'])
    }
    else if(num==3)
    {
        a = ['id3','cvalue3','lvalue3']
        for(i=0;i<a.length;i++)
        {
            arr.push(a[i])
        }
        for(i=0;i<arr.length;i++)
        {
            values[arr[i]] = $(document.getElementsByName(arr[i])[0]).val()
        }
        id = form.attr('sid')
    
        ele = document.getElementById(id)
        ele.firstChild.firstChild.setAttribute('for',values['id'])
        
        $(ele.firstChild.firstChild).text(values['lvalue'])
        cb1 = ele.firstChild.nextSibling;
        cb1.setAttribute('name',values['cname'])
        cb1.setAttribute('id',values['id1'])
        cb1.setAttribute('value',values['cvalue1'])
        $(cb1.nextSibling).text(values['lvalue1'])
        cb2 = cb1.nextSibling.nextSibling.nextSibling;
        cb2.setAttribute('id',values['id2'])
        cb2.setAttribute('value',values['cvalue2'])
        $(cb2.nextSibling).text(values['lvalue2'])

        cb3 = cb2.nextSibling.nextSibling.nextSibling;
        cb3.setAttribute('id',values['id3'])
        cb3.setAttribute('value',values['cvalue3'])
        $(cb3.nextSibling).text(values['lvalue3'])

    }
    else if(num==4)
    {
        a = ['id3','cvalue3','lvalue3','id4','cvalue4','lvalue4']
        for(i=0;i<a.length;i++)
        {
            arr.push(a[i])
        }
        for(i=0;i<arr.length;i++)
        {
            values[arr[i]] = $(document.getElementsByName(arr[i])[0]).val()
        }
        id = form.attr('sid')
    
        ele = document.getElementById(id)
        ele.firstChild.firstChild.setAttribute('id',values['id'])
        $(ele.firstChild.firstChild).text(values['lvalue'])
        cb1 = ele.firstChild.nextSibling;
        cb1.setAttribute('name',values['cname'])
        cb1.setAttribute('id',values['id1'])
        cv1 = cb1.setAttribute('value',values['cvalue1'])
        $(cb1.nextSibling).text(values['lvalue1'])
        cb2 = cb1.nextSibling.nextSibling.nextSibling;
        cb2.setAttribute('id',values['id2'])
        cb2.setAttribute('value',values['cvalue2'])
        $(cb2.nextSibling).text(values['lvalue2'])

        cb3 = cb2.nextSibling.nextSibling.nextSibling;
        cb3.setAttribute('id',values['id3'])
        cb3.setAttribute('value',values['cvalue3'])
        $(cb3.nextSibling).text(values['lvalue3'])

        cb4 = cb3.nextSibling.nextSibling.nextSibling;
        eid3 = cb4.setAttribute('id',values['id4'])
        cv4 = cb4.setAttribute('value',values['cvalue4'])
        cl4 = $(cb4.nextSibling).text(values['lvalue4'])
    }
}

function sselect(form,num){
    arr = ['id','lvalue','sname','svalue1','lvalue1','svalue2','lvalue2']
    id = form.attr('sid')
    
    ele = document.getElementById(id)
    sl2add = ele.firstChild.nextSibling.firstChild.nextSibling
    values = []
   
    if(num==2)
    {
        for(i=0;i<arr.length;i++)
        {
            values[i] = $(document.getElementsByName(arr[i])[0]).val()
        }
    }
    else if(num==3)
    {
        arr.push('svalue3')
        arr.push('lvalue3')
        
        for(i=0;i<arr.length;i++)
        {
            values[i] = $(document.getElementsByName(arr[i])[0]).val()
        }
        console.log(values)
    
    }
    else if(num==4)
    {
        arr.push('svalue3')
        arr.push('lvalue3')
        arr.push('svalue4')
        arr.push('lvalue4')
        
        for(i=0;i<arr.length;i++)
        {
            values[i] = $(document.getElementsByName(arr[i])[0]).val()
        }
        

    }
    $(ele.firstChild.firstChild).attr('for',values[0])
    $(ele.firstChild.firstChild).text(values[1])
    ele.firstChild.nextSibling.setAttribute("name",values[2])
    i = 3
    $(ele.firstChild.nextSibling).find('option').each(function(index,element){
        element.value = values[i]
        element.text = values[i+1]
        i+=2
  
     });
}

function ssubmit(form){
    type = document.getElementsByName('type')[0].getAttribute('value')
    value = document.getElementsByName('value')[0].getAttribute('value')
    id = form.attr('sid')
    
    ele = document.getElementById(id)
    ele.firstChild.setAttribute('type',type)
    ele.firstChild.setAttribute('value',value)
}


function update()
{
    form = $('#mbody').find('form').first()
    
    code = parseInt(form.attr('code'))
    
    switch (code)
    {
        case 1:
                slabel(form)
                break;
        case 2:
                
                stext(form)
                break;
        case 3:
                sradio(form,2)
                break;
        case 4:
                scheckbox(form,2)
                break;
        case 5:
                sselect(form,2)
                break;
        case 6:
                ssubmit(form)
                break;
        case 7:
            
            sradio(form,3);
                break;
        case 8:scheckbox(form,3)
                break;
        case 9:sselect(form,3)
                break;
        case 10:sradio(form,4)
                break;
        case 11:scheckbox(form,4)
                break;
        case 12:sselect(form,4)
                break;
        default:console.log("Not found");

    }
    $('#myModal').modal('hide');
}


function save(){
    
    
    $("button[class*=edit]").remove()
    
    children = document.getElementById('mine').children
    code = ``;
    
    $('#mine').find('.closediv').remove()
    for (var i = 0; i < children.length; i++)
    {
        code += $(children[i]).html();
        
        
    }
    
    
    $.post("writecode.php", {"code":code},function(data, status){
        alert('Successfully saved to code.html')
        
    });
    
}

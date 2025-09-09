

function ulabel(div) {
    var str='hi'
    $.post("../e01/e01cForm.php", function(data, status){
        $('#mbody').empty();
        $('#mbody').append(data);
        form = $('#mbody').find('form').first()
        
        form.removeAttr('action')
        size = div.firstChild.tagName
        text = div.firstChild.firstChild.innerText
        console.log(text)
        form.attr('code',1);
        form.attr('sid',div.getAttribute('id'))
        form.find(':submit').remove()
        document.getElementsByName('lvalue')[0].setAttribute('value',text)
        document.getElementsByName('lsize')[0].setAttribute('value',size)
        $('#myModal').modal('toggle')
    });
   
}

function utext(div) {
    $.post("../e02/e02cForm.php", function(data, status){
        $('#mbody').empty();
        $('#mbody').append(data);
        form = $('#mbody').find('form').first()
        form.attr('sid',div.getAttribute('id'))
        form.find(':submit').remove()
        form.removeAttr('action')
        form.attr('sid',div.getAttribute('id'))
        form.attr('code',2);
        size = div.firstChild.tagName
        text = div.firstChild.firstChild.innerText
        tbname = div.firstChild.nextSibling.getAttribute('name');
        document.getElementsByName('lvalue')[0].setAttribute('value',text)
        document.getElementsByName('lsize')[0].setAttribute('value',size)
        document.getElementsByName('tname')[0].setAttribute('value',tbname)
        $('#myModal').modal('toggle')
    });
    
        
}

function uradio(ele,num)
{
    console.log('num'+num)
    var eid,lvalue,rbvn,eid1,rv1,rl1,eid2,rv2,rl2;
    eid = ele.firstChild.firstChild.getAttribute('for')
    lvalue = $(ele.firstChild.firstChild).text()
    rb1 = ele.firstChild.nextSibling;
    rbvn = rb1.getAttribute('name')
    eid1 =rb1.getAttribute('id')
    rv1 = rb1.getAttribute('value')
    rl1 = $(rb1.nextSibling).text()
    rb2 = rb1.nextSibling.nextSibling.nextSibling;
    eid2 = rb2.getAttribute('id')
    rv2 = rb2.getAttribute('value')
    rl2 = $(rb2.nextSibling).text()
    
    if(num==2)
    {
        filename = "../e03/e03cForm.php"
        $.post(filename, function(data, status){
            $('#mbody').empty();
            $('#mbody').append(data);
            form = $('#mbody').find('form').first()
            form.find(':submit').remove()
            form.removeAttr('action')
            form.attr('sid',ele.getAttribute('id'))
            values = {
                'id':eid,'lvalue':lvalue,'rname':rbvn,'id1':eid1,'rvalue1':rv1,
                'lvalue1':rl1,'id2':eid2,'rvalue2':rv2,'lvalue2':rl2
            }
            form.attr('code',3);
            for(prop in values){
                document.getElementsByName(prop)[0].setAttribute('value',values[prop])
            }
            $('#myModal').modal('toggle')
        });

    }
    else if(num==3)
    {
        console.log('entered')
        filename = "../e07/e07cForm.php"
        $.post(filename, function(data, status){
            $('#mbody').empty();
            $('#mbody').append(data);
            form = $('#mbody').find('form').first()
            form.find(':submit').remove()
            form.removeAttr('action')
            form.attr('code',7);
            form.attr('sid',ele.getAttribute('id'))
            rb3 = rb2.nextSibling.nextSibling.nextSibling;
            eid3 = rb3.getAttribute('id')
            rv3 = rb3.getAttribute('value')
            rl3 = $(rb3.nextSibling).text()
            values = {
                'id':eid,'lvalue':lvalue,'rname':rbvn,'id1':eid1,'rvalue1':rv1,
                'lvalue1':rl1,'id2':eid2,'rvalue2':rv2,'lvalue2':rl2,'id3':eid3,
                'rvalue3':rv3,'lvalue3':rl3
            }
            
            for(prop in values){
                document.getElementsByName(prop)[0].setAttribute('value',values[prop])
            }
            $('#myModal').modal('toggle')
        });
        
    }
    else if(num==4)
    {
       filename = "../e10/e10cForm.php"
       rb3 = rb2.nextSibling.nextSibling.nextSibling;
       eid3 = rb3.getAttribute('id')
       rv3 = rb3.getAttribute('value')
       rl3 = $(rb3.nextSibling).text()
       rb4 = rb3.nextSibling.nextSibling.nextSibling;
       eid4 = rb4.getAttribute('id')
       rv4 = rb4.getAttribute('value')
       rl4 = $(rb4.nextSibling).text()
       $.post(filename, function(data, status){
            $('#mbody').empty();
            $('#mbody').append(data);
            form = $('#mbody').find('form').first()
            form.find(':submit').remove()
            form.attr('sid',ele.getAttribute('id'))
            form.removeAttr('action')
            form.attr('code',10);
            console.log(form)
            values = {
                'id':eid,'lvalue':lvalue,'rname':rbvn,'id1':eid1,'rvalue1':rv1,
                'lvalue1':rl1,'id2':eid2,'rvalue2':rv2,'lvalue2':rl2,'id3':eid3,
                'rvalue3':rv3,'lvalue3':rl3,'id4':eid4,'rvalue4':rv4,'lvlaue4':rl4
            }
            
            $('#myModal').modal('toggle')
        });
      
    }
    
}

function ucheckbox(ele,num) {
    
    var eid,lvalue,cbvn,eid1,cv1,cl1,eid2,cv2,cl2;
    eid = ele.firstChild.firstChild.getAttribute('for')
    lvalue = $(ele.firstChild.firstChild).text()
    cb1 = ele.firstChild.nextSibling;
    cbvn = cb1.getAttribute('name')
    eid1 =cb1.getAttribute('id')
    cv1 = cb1.getAttribute('value')
    cl1 = $(cb1.nextSibling).text()
    cb2 = cb1.nextSibling.nextSibling.nextSibling;
    eid2 = cb2.getAttribute('id')
    cv2 = cb2.getAttribute('value')
    cl2 = $(cb1.nextSibling.nextSibling.nextSibling.nextSibling).text()
    if(num==2)
    {
        filename = "../e04/e04cForm.php"
        $.post(filename, function(data, status){
            $('#mbody').empty();
            $('#mbody').append(data);
            form = $('#mbody').find('form').first()
            form.find(':submit').remove()
            form.removeAttr('action')
            form.attr('sid',ele.getAttribute('id'))
            form.attr('code',4);
            values = {
                'id':eid,'lvalue':lvalue,'cname':cbvn,'id1':eid1,'cvalue1':cv1,
                'lvalue1':cl1,'id2':eid2,'cvalue2':cv2,'lvalue2':cl2
            }
            for(prop in values){
                document.getElementsByName(prop)[0].setAttribute('value',values[prop])
            }
            
            $('#myModal').modal('toggle')
        });
    }
    else if(num==3)
    {
        filename = "../e08/e08cForm.php"
        cb3 = cb2.nextSibling.nextSibling.nextSibling;
        eid3 = cb3.getAttribute('id')
        cv3 = cb3.getAttribute('value')
        cl3 = $(cb3.nextSibling).text()
        
        $.post(filename, function(data, status){
            $('#mbody').empty();
            $('#mbody').append(data);
            form = $('#mbody').find('form').first()
            form.find(':submit').remove()
            form.removeAttr('action')
            form.attr('sid',ele.getAttribute('id'))
            form.attr('code',8);
            values = {
                'id':eid,'lvalue':lvalue,'cname':cbvn,'id1':eid1,'cvalue1':cv1,
                'lvalue1':cl1,'id2':eid2,'cvalue2':cv2,'lvalue2':cl2,'id3':eid3,
                'cvalue3':cv3,'lvalue3':cl3
            }
            
            for(prop in values){
                document.getElementsByName(prop)[0].setAttribute('value',values[prop])
            }
            $('#myModal').modal('toggle')
        });
    }
    else if(num==4)
    {
       filename = "../e11/e11cForm.php"
       cb3 = cb2.nextSibling.nextSibling.nextSibling;
       eid3 = cb3.getAttribute('id')
       cv3 = cb3.getAttribute('value')
       cl3 = $(cb3.nextSibling).text()
       cb4 = cb3.nextSibling.nextSibling.nextSibling;
       eid4 = cb4.getAttribute('id')
       cv4 = cb4.getAttribute('value')
       cl4 = $(cb4.nextSibling).text()
       $.post(filename, function(data, status){
            $('#mbody').empty();
            $('#mbody').append(data);
            form = $('#mbody').find('form').first()
            form.find(':submit').remove()
            form.attr('code',11);
            form.removeAttr('action')
            form.attr('sid',ele.getAttribute('id'))
            values = {
                'id':eid,'lvalue':lvalue,'cname':cbvn,'id1':eid1,'cvalue1':cv1,
                'lvalue1':cl1,'id2':eid2,'cvalue2':cv2,'lvalue2':cl2,'id3':eid3,
                'cvalue3':cv3,'lvalue3':cl3,'id4':eid4,'cvalue4':cv4,'lvlaue4':cl4
            }
            for(prop in values){
                $('[name="'+prop+'"]').each(function(index,element){
                    $(element).attr('value',values['lvalue'])
                })
            }
           
            $('#myModal').modal('toggle')
        });
    }
    
}

function uselect(ele,num) {
    var eid,lvalue,svn,sv1,sl1,sv2,sl2
    eid = $(ele.firstChild.firstChild).attr('for')
    lvalue = $(ele.firstChild.firstChild).text()
    svn = ele.firstChild.nextSibling.getAttribute("name")

   
    labels = []
    values = []
    $(ele.firstChild.nextSibling).find('option').each(function(index,element){
       labels.push(element.text)
       values.push(element.value)
 
    });
    if(num==2)
    {
        filename = "../e05/e05cForm.php"
        $.post(filename, function(data, status){
            $('#mbody').empty();
            $('#mbody').append(data);
            form = $('#mbody').find('form').first()
            form.find(':submit').remove()
            form.attr('code',5);
            form.attr('sid',ele.getAttribute('id'))
            form.removeAttr('action')
            values = {
                'id':eid,'lvalue':lvalue,'sname':svn,'svalue1':values[0],'lvalue1':labels[0],
                'svalue2':values[1],'lvalue2':labels[1]
            }
            for(prop in values){
                document.getElementsByName(prop)[0].setAttribute('value',values[prop])
            }
            $('#myModal').modal('toggle')
        });
        
    }
    else if(num==3)
    {
        filename = "../e09/e09cForm.php"
        
        $.post(filename, function(data, status){
            $('#mbody').empty();
            $('#mbody').append(data);
            form = $('#mbody').find('form').first()
            form.find(':submit').remove()
            form.removeAttr('action')
            form.attr('code',9);
            form.attr('sid',ele.getAttribute('id'))
            values = {
                'id':eid,'lvalue':lvalue,'sname':svn,'svalue1':values[0],'lvalue1':labels[0],
                'svalue2':values[1],'lvalue2':labels[1],'svalue3':values[2],'lvalue3':labels[2]
            }
            for(prop in values){
                document.getElementsByName(prop)[0].setAttribute('value',values[prop])
            }
            $('#myModal').modal('toggle')
        });
       
        
    }
    else if(num==4)
    {
       filename = "../e12/e12cForm.php"
     
       $.post(filename, function(data, status){
            $('#mbody').empty();
            $('#mbody').append(data);
            form = $('#mbody').find('form').first()
            form.find(':submit').remove()
            form.removeAttr('action')
            form.attr('sid',ele.getAttribute('id'))
            form.attr('code',12);
            values = {
                'id':eid,'lvalue':lvalue,'sname':svn,'svalue1':values[0],'lvalue1':labels[0],
                'svalue2':values[1],'lvalue2':labels[1],'svalue3':values[2],'lvalue3':labels[2],'svalue4':values[3],
                'lvalue4':labels[3]
            }
            for(prop in values){
                document.getElementsByName(prop)[0].setAttribute('value',values[prop])
            }
            $('#myModal').modal('toggle')
        });
      
    }
    
}

function usubmit(ele) {
    type = ele.firstChild.getAttribute('type')
    value = ele.firstChild.getAttribute('value')
    $.post("../e06/e06cForm.php", function(data, status){
        $('#mbody').empty();
        $('#mbody').append(data);
        form = $('#mbody').find('form').first()
        form.find(':submit').remove()
        form.removeAttr('action')
        form.attr('sid',ele.getAttribute('id'))
        form.attr('code',6);
        document.getElementsByName('type')[0].setAttribute('value',type)
        document.getElementsByName('value')[0].setAttribute('value',value)
        $('#myModal').modal('toggle')
    });
}
function O(obj)
{
    if (typeof obj == 'object') return obj
    else return document.getElementById(obj)
}

function S(obj)
{
    return O(obj).style
}

function C(name)
{
    var elements = document.getElementsByTagName('*')
    var objects = []
    
    for(var i = 0; i<elements.length ; ++i)
    {
        if (elemets[i].className == name)
        {
            objects.push(elements[i])
        }
    }
    
    return objects
}

function toggleElement(x) {
    var x = O(x);
    if (x.style.display == 'block') {
        x.style.display = 'none';
    }
    else {
        x.style.display = 'block';
    }
}
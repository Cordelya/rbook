/*
 * rbook Recipe Management System
 * Copyright (C) 2005 Andrew Violette andrew@andrewviolette.net
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * $Id: recipe.js,v 1.15 2007/04/01 14:38:36 maschine Exp $
 */

function isReturn(event) {
  var w = (event.charCode) ? event.charCode : (event.which ? event.which : event.keyCode);
  return w == 13;
}

function addOnReturn(event, clickWhat, focusOnWhat) {
  cancelPropagation(event);
  if (isReturn(event)) {
    $(clickWhat).click();
    $(focusOnWhat).focus();
  }
}

function cancelPropagation(event) {
  if (isReturn(event)) {
      Event.stop(event);
  }
}

function printRecipe() {
	window.print();
}

function EmailVerifyControl(component, action) {
    this.component = component;
    this.action = action;
    var o = this;
    Event.observe(component, 'keyup', this.validate.bindAsEventListener(this));
    Event.observe(component, 'blur', this.validate.bindAsEventListener(this));
}

EmailVerifyControl.prototype = {
  validate : function(evt) {
    evt = (evt) ? evt : event;
    var elem = Event.element(evt);
    var pattern = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
    var str = elem.value;
    var valid = str.match(pattern);
    this.action.call(this, valid);
  }
}

function NameVerifyControl(component, action) {
  this.component = component;
  this.action = action;
  var o = this;
  this.component.onkeyup = function(evt) {
    o.validate.call(o, evt);
  }
}

NameVerifyControl.prototype.validate = function(evt) {
  evt = (evt) ? evt : event;
  var elem = (evt.target) ? evt.target : evt.srcElement;
  var str = elem.value;
  var pattern = /^[a-zA-Z]+/;
  var valid = str.match(pattern);
  this.action.call(this, valid != null);
}

function AreYouSureVerification(button, action, rb) {
    this.displayZone = button + "Span";
    this.action = action;
    this.rb = rb;
    var o = this;
    Event.observe(button, 'click', this.areYouSure.bindAsEventListener(this));
}

AreYouSureVerification.prototype = {
  areYouSure : function() {
    $(this.displayZone).style.display = "inline";
  },

  no : function() {
    $(this.displayZone).style.display = "none";
  },

  yes : function() {
    if (this.action instanceof Object) {
      this.action();
      $(this.displayZone).style.display = "none";
    } else {
      location.href = this.action;
    }
  },

  prepare : function(name) {
    document.write('<span id="' + this.displayZone +
                   '" style="display:none" class="warning">&nbsp;' + this.rb.ays + '&nbsp;<a class="prompt" href="#" onclick="' +
                   name + '.no()">' + this.rb.no + '</a>&nbsp;/&nbsp;<a href="javascript:' +
                   name + '.yes()">' + this.rb.yes + '</a></span>');
  }
};


function RecipeEditor(rb, options) {
  this.rb = rb;
  this.lastOne = 0;
  this.options = options;
}

RecipeEditor.prototype = {

  preventSubmission : function(evt) {
    evt = (evt) ? evt : event;
    cancelPropagation(evt);
    if (isReturn(evt)) {
      var elem = (evt.target) ? evt.target : evt.srcElement;
      var divId = elem.getAttribute("id");
      divId = divId.substring(10, divId.length);
      $("descriptionText" + divId).focus();
    }
    return true;
  },

  advanceStep: function(event, stepId) {
    if (!isReturn(event)) {
      return;
    }
    var steps = $("steps");
    var rows = steps.getElementsByTagName("li");
    if (rows.length == (Number(stepId) + 1)) {
      var step = Number(stepId) + 1;
      addOnReturn(event, 'addStepButton', 'stepInput' + step);
    } else {
      cancelPropagation(event);
      var b = Number(stepId);
      var f = $('stepInput' + (b + 1));
      if (f != null) {
        f.focus();
      }
    }
  },

  addOnReturnProcessing: function(evt) {
    evt = (evt) ? evt : event;
    var elem = Event.element(evt);
    var divId = elem.getAttribute("id");
    divId = divId.substring(15, divId.length);
    if (isReturn(evt)) {
      cancelPropagation(evt);
      this.addIngredient(divId);
      $("amountText" + divId).focus();
    }
  },

  deleteIngredient: function(index, tableId) {
    var table = $(tableId);
    var tr = $("row" + index);
    if (tr != null) {
      table.removeChild(tr);
    }
  },

  deleteIngredientSet: function(setId) {
    var divid = $("div-" + setId);
    var setSection = $("setSection");
    if (divid == null || setSection == null) {
      return;
    }
    setSection.removeChild(divid);
  },

  addIngredientSet : function() {
    var divId = "c" + (new Date()).getTime();
    this.editIngredientSet(divId, this.rb.Ingredients, null);
    var e = $("nameField" + divId);
    if (e != null) {
      e.focus();
    }
  },

  addIngredientProcessing : function (evt) {
    evt = (evt) ? evt : event;
    var elem = Event.element(evt);
    var divId = elem.getAttribute("id");
    divId = divId.substring(9, divId.length);
    this.addIngredient(divId);

  },


  editIngredientSet : function (divId, name, values) {
    var thead = Builder.node("thead",
        [Builder.node("tr",
            [Builder.node("th", this.rb.Amount),
                Builder.node("th", this.rb.Description),
                Builder.node("th", " ")])]);

    var o = this;

    var tbody = Builder.node("tbody", {id: "set" + divId},
        [Builder.node("tr",
            [Builder.node("td", {className: "amountCell"},
                [Builder.node("input", {id: "amountText" + divId,
				  name: "amount-" + divId + "-999",
                  type: "text",
                  size: "15",
                  maxlength: this.options.amountLength})]),
                Builder.node("td", {className: "descCell"},
                    [Builder.node("input", {id: "descriptionText" + divId,
					  name: "desc-" + divId + "-999",
                      type: "text",
                      size: "30",
                      maxlength: this.options.ingredientLength})]),
                Builder.node("td", {},
                    [Builder.node("input", {id: "addButton" + divId,
                      type: "button",
                      value: this.rb.Add})])])]
        );

    if (values != null) {
      for (var i = 0; i < values.length; i++) {
        this.addIngredientRow(tbody, values[i][0], values[i][1], divId, i % 2);
      }
    }

    var labelFor = new Object();
    // had to build this way since 'for' is a keyword
    labelFor['for'] = "nameField" + divId;
    labelFor['style'] = "margin-right: 5px";

	var deleteText = this.buildButton(this.options.deleteIcon, 'deleteIcon.gif',
									  this.rb.deleteButton);

    var setDiv =
        Builder.node("div", {id: "div-" + divId},
            [Builder.node("div", {style: "padding-bottom: 1.5em"},
                [Builder.node("label", labelFor, 'Name:'),
                    Builder.node("input", {id: "nameField" + divId,
                      type: "text",
                      name: "name" + divId,
                      value: name,
                      maxlength: "30",
                      size: "30"}),
                    Builder.node("a", {href: "javascript:re.deleteIngredientSet('" + divId + "')",
                      className: "deleteButton",
                      style: "margin-left: 5px",
					  title: rb.deleteIngredientSetTooltip
                    }, deleteText )]),
                Builder.node('div"', {className: "helptext"}, this.rb.SortIngredient),
                Builder.node("div", {style: "margin-bottom: 30px"},
                    [Builder.node("table", {className: "ingredientTable"},
                        [thead, tbody])])]);

    var setsDiv = $("setSection");
    setsDiv.appendChild(setDiv);

    Event.observe("addButton" + divId, 'click', this.addIngredientProcessing.bindAsEventListener(this));
    Event.observe("amountText" + divId, 'keypress', this.preventSubmission.bindAsEventListener(this));
    Event.observe("descriptionText" + divId, 'keypress', this.addOnReturnProcessing.bindAsEventListener(this));
    this.makeIngredientsSortable(o, tbody, divId);

  },
  buildButton : function(hasImg, imgName, text) {
	if(hasImg && this.options.buttonDisplayMode.length == 0 ||
	   this.options.buttonDisplayMode == 'both') {
		return [Builder.node("img", {className: 'icon', src: this.options.skinImages + imgName}), text];

	} else if(hasImg && this.options.buttonDisplayMode == 'icon') {
		return [Builder.node("img", {className: 'icon', src: this.options.skinImages + imgName})];
	} else {
		return text;
	}
		
			
			

  },
  makeIngredientsSortable: function(o, tbody, divId) {
    Sortable.create(tbody.getAttribute("id"), {
      tag : 'tr',
      onChange : function() {
        o.resortIds.call(o, tbody, divId);
      }
    });
  },

  resortIds : function(tbody, divId) {
    var trs = tbody.getElementsByTagName("tr");
    var o = this;
    var counter = 0;
    $A(trs).each(function(tr) {
      var tds = tr.getElementsByTagName("td");
	  odd = counter % 2;
      counter = counter + 1;
      $A(tds).each(function(td) {
        if (Element.hasClassName(td,"amountCell")) {
          $A(td.getElementsByTagName('input')).each(
              function(input) {
                input.setAttribute("name", "amount-" + divId + "-" + counter);
              });
        } else if (Element.hasClassName(td, "descCell")) {
          $A(td.getElementsByTagName('input')).each(
              function(input) {
                input.setAttribute("name", "desc-" + divId + "-" + counter);
              }
              );
        }
      })
    });
  },

  addIngredientRow : function (tbody, amount, desc, set, odd) {
    if (desc == null || desc.length == 0) {
      return;
    }
    var table = tbody;

    var l = table.childNodes;
    var lastTr = null;
    for (var i = 0; i < l.length; i++) {
      if (l.item(i).nodeName == "TR" || l.item(i).nodeName == "tr") {
        lastTr = l.item(i);
		if(odd == undefined) {
			odd = i % 2;
		}
      }
    }

	var rowClass = "even ";
	if(odd) {
		rowClass = "odd ";
	}
    this.lastOne = this.lastOne + 1;
	var deleteText = this.buildButton(this.options.deleteIcon, 'deleteIcon.gif',
									  this.rb.deleteButton);

    var tr = Builder.node("tr", {id: "row" + this.lastOne}, [
        Builder.node("td", {className: rowClass + "amountCell"}, [
            Builder.node("input", {name: "amount-" + set + "-" + this.lastOne,
              size: "15",
              type: "text",
              maxlength: this.options.amountLength,
              value: amount})]),
        Builder.node("td", {className: rowClass + 'descCell'}, [
            Builder.node("input", {name: "desc-" + set + "-" + this.lastOne,
              size: "30",
              type: "text",
              maxlength: this.options.ingredientLength,
              value: desc})]),
        Builder.node("td", {className: rowClass + 'actionCell'}, [
            Builder.node("a", {href: "javascript:re.deleteIngredient(" +
                                     this.lastOne + ",'" + tbody.getAttribute("id") + "')",
              className: 'ebutton'}, deleteText)])]);
    if (lastTr != null) {
      table.insertBefore(tr, lastTr);
    } else {
      table.appendChild(tr);
    }
  },

  addIngredient : function (set) {
    var att = $("amountText" + set);
    var amount = att.value;
    var desc = $("descriptionText" + set).value;
    if (desc == null || desc.length == 0) {
      return;
    }

    this.addIngredientRow($("set" + set),
        amount, desc, set);
    att.value = "";
    $("descriptionText" + set).value = "";
    Sortable.destroy($("set" + set));
    this.makeIngredientsSortable(this, $("set" + set), set);
  },

  advanceStepHandler : function (evt) {
    evt = (evt) ? evt : event;
    var elem = Event.element(evt);
    var stepId = elem.getAttribute("id");
    stepId = stepId.substring(9, stepId.length);
    this.advanceStep(evt, stepId);
  },

  addStepWithText : function (text, repopulate) {
    var steps = $('steps');
    var count = steps.getElementsByTagName('li').length;
    var o = this;
    var taFunc = function(evt) {
      o.advanceStepHandler.call(o, evt);
    };
	var deleteText = this.buildButton(this.options.deleteIcon, 'deleteIcon.gif',
									this.rb.deleteButton);
    var row =
		Builder.node("li", {id: 'stepRow' + count, className: 'taRow'},
                [Builder.node("textarea", {name: 'step' + count,
                  className: 'editStep',
                  rows: "5",
                  cols: "50",
                  id: 'stepInput' + count}, text),
				 Builder.node("a", {href: "javascript:re.deleteStep(" +count +
                                      ")"}, deleteText)]);

    steps.appendChild(row);
    $("stepInput" + count).onkeypress = taFunc;
    $('stepInput' + count).focus();
    if (repopulate) {
      this.repopulateSteps();
    }
  },
  makeStepsSortable: function() {
    var o = this;
    Sortable.create("steps", {
      tag : 'li',
      onChange : function() {
        o.repopulateSteps.call(o);
      }
    });
  },
  addStep : function () {
    this.addStepWithText(null, true);
  },

  repopulateSteps : function () {
    var l = $('steps');
    var trs = l.getElementsByTagName('li');
    for (var i = 0; i < trs.length; i++) {
      trs[i].setAttribute('id', 'stepRow' + i);
      var tmps = trs[i].getElementsByTagName("textarea");
      tmps[0].setAttribute("name", "step" + i);
	  tmps = trs[i].getElementsByTagName("a");
	  tmps[0].setAttribute("href", "javascript:re.deleteStep(" + i + ")");
    }
  },


  selectTab : function(id) {
	return function(event) {
		$A(['information', 'ingredients', 'step', 'note']).each(function(node) {
			var tab = node + 'Tab';
			var module = node + 'Module';
			if(id == module) {
				Element.show(module);			
				var selectedTab = $('selectedTab');
				if(selectedTab) {
					selectedTab.value = node;
				}
				if(!Element.hasClassName(tab, 'selectedTab1')) {
					Element.addClassName(tab, 'selectedTab1');
					Element.addClassName($(tab).parentNode, 'selectedTab1');
					Element.removeClassName($(tab).parentNode, 'unselectedTab1');
					Element.removeClassName(tab, 'unselectedTab1');
				}
			} else {
				if(Element.hasClassName(tab, 'selectedTab1')) {
					Element.removeClassName(tab, 'selectedTab1');
					Element.removeClassName($(tab).parentNode, 'selectedTab1');
					Element.addClassName(tab, 'unselectedTab1');
					Element.addClassName($(tab).parentNode, 'unselectedTab1');
					
				}
				Element.hide(module);
			}
		});
	}
  },

  addCat : function () {
  var catList = document.getElementById("categoryList");
  var categories = document.getElementById("categories");
  if(categories.value == "")
	categories.value = catList.options[catList.selectedIndex].label;
  else
	categories.value += "," + catList.options[catList.selectedIndex].label;
  },  

  addNote : function () {
    var note = $('noteArea');
    var d = $("noteDiv");
    $('noteTextArea').innerHTML = note.value;
    d.innerHTML = note.value;
  },

  initEditor : function() {
    var e = $('titleField');
    e.focus();
	window.scrollTo(0, 0);
    try {
      if (e.setSelectionRange) {
        e.setSelectionRange(0, e.value.length);
      } else if (e.createTextRange) {
        var m = e.createTextRange();
        m.moveStart('character', 0);
        m.moveEnd('character', e.value.length);
        m.select();
      }
    } catch(e) {
    }
	var informationSelect = this.selectTab('informationModule');
  	Event.observe('informationTab', 'click', informationSelect.bindAsEventListener(this));
	var ingredientsSelect = this.selectTab('ingredientsModule');
	Event.observe('ingredientsTab', 'click', ingredientsSelect.bindAsEventListener(this));
	var stepSelect = this.selectTab('stepModule');
	Event.observe('stepTab', 'click', stepSelect.bindAsEventListener(this));
	var noteSelect = this.selectTab('noteModule');
	Event.observe('noteTab', 'click', noteSelect.bindAsEventListener(this));
	if(this.options.selectedTab) {
		if(this.options.selectedTab == 'note') {
			noteSelect();
		} else if(this.options.selectedTab == 'step') {
			stepSelect();
		} else if(this.options.selectedTab == 'ingredients') {
			ingredientsSelect();
		} else {
			informationSelect();
		}
	} else {
		informationSelect();
	}
  },

  deleteStep : function (step) {
    var steps = $("steps");
    steps.removeChild($("stepRow" + step));
    this.repopulateSteps();
  }
};

function deleteUser() {
  $("actionField").value = 'delete';
  $("users").submit();
}

function newUser() {
  $("actionField").value = 'new';
  $("users").submit();
}
function deleteCategory() {
  $("actionField").value = 'delete';
  $("categories").submit();
}

function newCategoryOnReturn(evt) {
  if (isReturn(evt)) {
    cancelPropagation(evt);
    $("newButton").click();
  }
}

function changeVolumes() {
  var v = $('volume').value;
  var rates = new Array(8);
  rates[0] = new Array(1, 0.33, 0.167, 0.0208, 0.0104, 0.0078125, 0.005, 0.0013029);
  rates[1] = new Array(3, 1, 0.5, 0.0625, 0.0313, 0.015625, 0.01478125, 0.00390625);
  rates[2] = new Array(6, 2, 1, 0.125, 0.0625, 0.03125, 0.0295625, 0.0078125);
  rates[3] = new Array(48, 16, 8, 1, 0.5, 0.25, 0.2365, 0.0625);
  rates[4] = new Array(96, 32, 16, 2, 1, 0.5, 0.473, 0.125);
  rates[5] = new Array(192, 64, 32, 4, 2, 1, 0.946, 0.25);
  rates[6] = new Array(200, 66.67, 33.33, 4.23, 2.12, 1.06, 1, 0.26);
  rates[7] = new Array(768, 256, 128, 16, 8, 4, 3.785, 1);

  var c = $("sourceq").value;
  for (var i = 0; i < 8; i++) {
    var val = v * rates[c][i];
    if (val >= 0.99 && val < 1) {
      val = 1;
    }
    $("v" + i).innerHTML = buildFraction(val);
  }
}

function buildFraction(val) {
    var f = numericPart(val);
    if (f != 0) {
      f = f + " ";
    } else {
      f = "";
    }
	return (f + fractionalPart(val));
}

function numericPart(num) {
  if (num - Math.ceil(num) > 0.9735) {
    return Math.ceil(num) + 1;
  }
  return Math.floor(num);
}

function fractionalPart(num) {
  var f = num - (Math.floor(num));
  if (f < 0.03125) {
    return "";
  } else if (f <= 0.0625) {
    return "1/16";
  } else if (f <= 0.1875) {
    return "1/8";
  } else if (f <= 0.2915) {
    return "1/4";
  } else if (f <= 0.4167) {
    return "1/3";
  } else if (f <= 0.5625) {
    return "1/2";
  } else if (f <= 0.625) {
    return "5/8";
  } else if (f <= 0.729) {
    return "2/3";
  } else if (f <= 0.81) {
    return "3/4";
  } else if (f <= 0.9735) {
    return "7/8";
  }
  return "";

}

function changeMass() {
  var v = $('mass').value;
  var rates = new Array(4);
  rates[0] = new Array(1, 0.001, 0.000035, 0.00000221);
  rates[1] = new Array(1000, 1, 0.035, 0.0022);
  rates[2] = new Array(28350, 28.35, 1, 0.0625);
  rates[3] = new Array(450000, 453.92, 16, 1);

  var c = $('massq').value;
  for (var i = 0; i < 4; i++) {
    var val = v * rates[c][i];
    if (val >= 0.99 && val < 1) {
      val = 1;
    }
    document.getElementById("m" + i).innerHTML = val;
  }
}

function SendInviteValidator() {
  this.nameValid = false;
  this.emailValid = false;
}

UserNameValidator = Class.create();

UserNameValidator.prototype = {
	initialize : function(control, rootPath) {
		this.field = control;
		this.rootPath = rootPath;
		Event.observe(control, 'blur', this.validateUserName.bindAsEventListener(this));
	},

	validateUserName : function() {
      var url = this.rootPath + "user/validate/" + $(this.field).value;
	  var o = this;
      var f = function(request) {
        o.handleUserNameVerification.call(o, request.responseText);
      }
      var ajax = new Ajax.Request(url, {
        method: 'get',
        onComplete: f
      });
	},
	
	handleUserNameVerification : function(text) {
		if(!$(this.field + "Label")) {
			return;
		}
		if(text == "true") {
			Element.addClassName(this.field + "Label", "error");
		} else {
			Element.removeClassName(this.field + "Label", "error");
		}
	}
	
}

SendInviteValidator.prototype = {
  handleEmailValid : function(valid) {
    if (valid) {
      var field = $("email");
      var o = this;
      var f = function(request) {
        o.handleEmailVerification.call(o, request.responseText);
      }
      var url = "verifyUser.php";
      var pars = "e=" + field.value;
      var ajax = new Ajax.Request(url, {
        method: 'get',
        parameters: pars,
        onComplete: f
      });
    } else {
      $("submitButton").disabled = true;
    }
  },

  handleEmailVerification : function(text) {
    var errorDiv = $("errorDiv");
    if ("true" == text) {
      errorDiv.style.display = "block";
      errorDiv.innerHTML = "User already exists.";
      this.emailValid = false;
    } else {
      errorDiv.style.display = "none";
      this.emailValid = true;
    }
    this.enableIfValid();
  },

  enableIfValid : function() {
    $("submitButton").disabled =
    !(this.nameValid && this.emailValid);
  },

  handleNameValid : function(valid) {
    this.nameValid = valid;
    this.enableIfValid();
  }
}

function initInvite() {
  var v = new SendInviteValidator();
  var f = function(valid) {
    v.handleEmailValid.call(v, valid);
  }
  var field = $("email");
  new EmailVerifyControl(field, f);
  f = function(valid) {
    v.handleNameValid.call(v, valid);
  }
  field = $("name");
  new NameVerifyControl(field, f);
}

function ReceiveInviteValidator() {
}

function categoryHelp(evt) {
  $("helpbox").innerHTML = "hello";
  Element.toggle($("helpbox"));
}

function buildNumber(fractional) {
	p = fractional.match(/^\s*(\d+)\s*\/\s*(\d+)/);
	if(p) {
		return parseFloat(p[1]) / parseFloat(p[2]);
	}
	p = fractional.match(/^\s*(\d+)\s+(\d+)\/(\d+)/);
	if(p) {

		return parseInt(parseFloat(p[1])) + (parseFloat(p[2]) / parseFloat(p[3]));
	}
	return parseFloat(fractional);
}

function viewRecipeOnLoad(evt) {
	var serves = $("servesField");
	if(serves) {
		var initialServes = serves.innerHTML;
		var slider = new Control.Slider('handle1', 'track1', {
										range: $R(0, 30), 
										values: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],
										sliderValue: parseInt(initialServes),
										onChange: function(v,s) {
											serves.innerHTML = v;
											var cells = document.getElementsByClassName('amountCell', 'ingredientsSection');
											var re = /^(\d|\/|\ |\.)+(.*)/;
											$A(cells).each(function(f) {
												var x = f.innerHTML;
												if(!f.origServes) {
													var num = x.match(re);
													if(num) {
														f.otherStuff = num[2];
														num = buildNumber(num[0]);
														if(!f.origValue && num) {
															f.origValue = x;
															f.origServes = parseFloat(initialServes);
															f.oneServing = num / f.origServes;
														}
													}	
												}
												if(f.origServes && f.origServes != 0) {
													var val = f.oneServing * v;
													f.innerHTML = buildFraction(val) + " " + f.otherStuff;
												} else {
													if(!Element.hasClassName(f, 'warning')) {
														Element.addClassName(f, 'warning');
													}
												}

											});
										}});
		Element.hide('track1');
		Event.observe(serves, "click", function(evt) {
			Element.toggle('track1');
			Element.toggle('sliderHelp');
		});
	}
	var aacl = $('addACommentLink');
	if(aacl) {
		Event.observe(aacl, 'click', function(evt) {
			Element.hide(aacl);
			Element.show('addCommentSection');
		}); 
	}
}

function guestbookAddComment(evt) {
	var aacl = $('addACommentLink');
	if(aacl) {
		Event.observe(aacl, 'click', function(evt) {
			Element.hide(aacl);
			Element.show('addCommentSection');
		}); 
	}
}

function toggleComments(evt) {
}
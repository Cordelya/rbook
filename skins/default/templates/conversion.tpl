{include file="skin:header.tpl"}
<h2>{getMessage key=VolumetricConversions}</h2>
<div>
<label for="sourceq">{getMessage key=Amount}:</label><input type="text" onkeyup="changeVolumes();" maxlength="10" size="10" id="volume"/>
<select style="width:10em" id="sourceq" onchange="changeVolumes()">
  <option value="0">{getMessage key=Teaspoons}</option>
  <option value="1">{getMessage key=Tablespoons}</option>
  <option value="2">{getMessage key=FluidOunces}</option>
  <option value="3">{getMessage key=Cups}</option>
  <option value="4">{getMessage key=Pints}</option>
  <option value="5">{getMessage key=Quarts}</option>
  <option value="6">{getMessage key=Liters}</option>
  <option value="7">{getMessage key=Gallons}</option>
</select>

<table class="withborder conversionTable">
<thead>
<tr>
<td class="measurement">{getMessage key=Measurement}</td>
<td class="quantity">{getMessage key=Value}</td>
</tr>
</thead>
<tbody>
<tr>
<td class="measurement">{getMessage key=Teaspoons}</td>
<td id="v0">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=Tablespoons}</td>
<td id="v1">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=FluidOunces}</td>
<td id="v2">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=Cups}</td>
<td id="v3">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=Pints}</td>
<td id="v4">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=Quarts}</td>
<td id="v5">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=Liters}</td>
<td id="v6">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=Gallons}</td>
<td id="v7">&nbsp;</td>
</tr>
</tbody>
</table>
</div>

<h2>{getMessage key=MassConversions}</h2>
<label for="mass">{getMessage key=Amount}:</label><input onkeyup="changeMass()" type="text" id="mass" maxlength="10" size="10"/>
<select style="width: 10em" id="massq" onchange="changeMass()">
<option value="0">{getMessage key=Milligrams}</option>
<option value="1">{getMessage key=Grams}</option>
<option value="2">{getMessage key=Ounces}</option>
<option value="3">{getMessage key=Pounds}</option>
</select>
<table class="withborder conversionTable">
<thead>
<tr>
<td class="measurement">{getMessage key=Measurement}</td>
<td>{getMessage key=Value}</td>
</tr>
</thead>
<tbody>
<tr>
<td class="measurement">{getMessage key=Milligrams}</td>
<td id="m0">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=Grams}</td>
<td id="m1">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=Ounces}</td>
<td id="m2">&nbsp;</td>
</tr>
<tr>
<td class="measurement">{getMessage key=Pounds}</td>
<td id="m3">&nbsp;</td>
</tr>
</tbody>
</table>
{include file="skin:footer.tpl"}

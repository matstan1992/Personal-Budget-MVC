let amountInput = document.getElementById("amount");
let dateInput = document.getElementById("date");
let categoryInput = document.getElementById("category");
const divLimit = document.getElementById("limit");
let limitInfo = document.getElementById("limitInfo");
let monthlySpendInfo = document.getElementById("monthlySpendInfo");
let resultInfo = document.getElementById("resultInfo");

const checkCategory = async () => {
	let categoryId = categoryInput.value;
	let date = dateInput.value;
	let categoryLimit;
	
	await getLimitForCategory(categoryId).then(data => {
		categoryLimit = Number(data);
	});
	
	if (categoryLimit > 0)
	{
		checkLimit(categoryLimit, categoryId, date);
	}
	else 
	{
		divLimit.classList.add("limit");
	}
};


const getLimitForCategory = async (id) => {
	try 
	{
		const response = await fetch(`/expense/getCategoryLimit/${id}`);
		const data = await response.json();
		return data[0].category_limit;
	}
	catch (error)
	{
		console.error("Error:", error);
	}
};


const getSumOfExpensesForSelectedMonth = async (id, date) => {
	try 
	{
		const response = await fetch(`/expense/getExpensesDate/${id}/${date}`);
		const data = await response.json();
		let sum = 0;
		
		for (let i = 0; i < data.length; i++)
		{
			sum += Number(data[i].amount);
		}
		
		return sum;
	}
	catch (error)
	{
		console.error("Error:", error);
	}
};

const checkLimit = async (categoryLimit, categoryId, date) => {
	let sumOfExpensesMonthly = 0;
	await getSumOfExpensesForSelectedMonth(categoryId, date).then(data => {
		sumOfExpensesMonthly = data;
	});
	
	renderOnDom(categoryLimit, sumOfExpensesMonthly);
};

const renderOnDom = (categoryLimit, sumOfExpensesMonthly) => {
	divLimit.classList.remove("limit");
	limitInfo.textContent = `${categoryLimit}`;
	monthlySpendInfo.textContent = `${sumOfExpensesMonthly}`;
	let restAmount = format((Number(categoryLimit) - Number(amountInput.value) - Number(sumOfExpensesMonthly)),2);
	
	if ((Number(amountInput.value) + Number(sumOfExpensesMonthly)) > categoryLimit)
	{
		resultInfo.style.color = "red";
		resultInfo.textContent = "Limit w tym miesiącu jest już przekroczony";
	}
	else
	{
		resultInfo.style.color = "green";
		resultInfo.textContent = `W tym misiącu możesz jeszcze wydać: ${restAmount} zł`;
	}
	
};

function format(liczba, lmpp) {
	ile = ""+Math.round(liczba*Math.pow(10,lmpp))/Math.pow(10,lmpp);
	if (ile.indexOf(".")<0) ile+=".0";
	while ((ile.length-ile.indexOf(".")-1)<lmpp) ile = ile+"0";
	return ile;
}

categoryInput.addEventListener('change', async () => {
	await checkCategory();
});

dateInput.addEventListener('change', async () => {
	await checkCategory();
});

amountInput.addEventListener('input', async () => {
	await checkCategory();
});

document.onload = checkCategory();
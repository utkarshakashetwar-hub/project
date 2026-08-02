import streamlit as st
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns

# -----------------------------
# SESSION STATE
# -----------------------------
if 'expenses' not in st.session_state:

    st.session_state.expenses = pd.DataFrame(
        columns=['Date', 'Category', 'Amount', 'Description']
    )

# -----------------------------
# ADD EXPENSE FUNCTION
# -----------------------------
def add_expense(Date, Category, Amount, Description):

    new_expense = pd.DataFrame(
        [[Date, Category, Amount, Description]],
        columns=st.session_state.expenses.columns
    )

    st.session_state.expenses = pd.concat(
        [st.session_state.expenses, new_expense],
        ignore_index=True
    )

# -----------------------------
# SAVE FUNCTION
# -----------------------------
def save_expenses():

    st.session_state.expenses.to_csv(
        "expenses.csv",
        index=False
    )

    st.success("Expenses Saved Successfully!")

# -----------------------------
# LOAD FUNCTION
# -----------------------------
def load_expenses():

    try:

        st.session_state.expenses = pd.read_csv("expenses.csv")

        st.success("Expenses Loaded Successfully!")

    except:

        st.error("No saved file found!")

# -----------------------------
# VISUALIZATION FUNCTION
# -----------------------------
def visualize_expenses():

    if st.session_state.expenses.empty:

        st.warning("No expenses available!")
        return

    st.subheader("Bar Chart")

    fig, ax = plt.subplots(figsize=(8, 5))

    sns.barplot(
        x='Category',
        y='Amount',
        data=st.session_state.expenses,
        ax=ax
    )

    plt.xticks(rotation=45)

    st.pyplot(fig)

    # PIE CHART

    st.subheader("Pie Chart")

    category_sum = st.session_state.expenses.groupby(
        'Category'
    )['Amount'].sum()

    fig2, ax2 = plt.subplots()

    ax2.pie(
        category_sum,
        labels=category_sum.index,
        autopct='%1.1f%%'
    )

    st.pyplot(fig2)

# -----------------------------
# TITLE
# -----------------------------
st.title("💰 DevDuniya Expense Tracker")

# -----------------------------
# SIDEBAR
# -----------------------------
with st.sidebar:

    st.header("➕ Add Expense")

    date = st.date_input("Date")

    category = st.selectbox(
        "Category",
        ['Food', 'Transport', 'Entertainment', 'Utilities', 'Other']
    )

    amount = st.number_input(
        "Amount",
        min_value=0.0,
        format="%.2f"
    )

    description = st.text_input("Description")

    if st.button("Add Expense"):

        add_expense(
            date,
            category,
            amount,
            description
        )

        st.success("Expense Added!")

    # FILE OPERATIONS

    st.header("📁 File Operations")

    if st.button("Save Expenses"):

        save_expenses()

    if st.button("Load Expenses"):

        load_expenses()

    # VISUALIZATION BUTTON

    st.header("📊 Visualization")

    if st.button("Visualize Expenses"):

        visualize_expenses()

# -----------------------------
# TOTAL EXPENSE
# -----------------------------
if not st.session_state.expenses.empty:

    total = st.session_state.expenses['Amount'].sum()

    st.metric(
        "Total Expenses",
        f"₹ {total:.2f}"
    )

# -----------------------------
# SHOW DATA
# -----------------------------
st.header("📋 Expense Records")

st.write(st.session_state.expenses)

# -----------------------------
# DELETE EXPENSE
# -----------------------------
if not st.session_state.expenses.empty:

    st.header("❌ Delete Expense")

    selected_index = st.selectbox(
        "Select Row Index",
        st.session_state.expenses.index
    )

    if st.button("Delete Selected Expense"):

        st.session_state.expenses.drop(
            selected_index,
            inplace=True
        )

        st.session_state.expenses.reset_index(
            drop=True,
            inplace=True
        )

        st.success("Expense Deleted!")
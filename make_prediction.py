import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import LabelEncoder
from sklearn.metrics import accuracy_score
import joblib
from sqlalchemy import create_engine
import mysql.connector

# Database connection
engine = create_engine("mysql+pymysql://root:@localhost/fyp_progress_recorder")

# Load data for training
df = pd.read_sql("SELECT * FROM training_data", engine)

# Print data for debugging
print("Loaded Data:")
print(df.head())
print("Number of records in the dataset:", len(df))

# Convert categorical columns to numeric (excluding project_id)
label_columns = ['assignment_status']
for col in label_columns:
    le = LabelEncoder()
    df[col] = le.fit_transform(df[col].astype(str))

# Convert feedback columns to numeric
feedback_mapping = {
    'Excellent': 5,
    'Good': 4,
    'Average': 3,
    'Fair': 2,
    'Poor': 1
}
df['avg_meeting_feedback'] = df['avg_meeting_feedback'].map(feedback_mapping)
df['avg_presentation_feedback'] = df['avg_presentation_feedback'].map(feedback_mapping)

# Drop unnecessary columns safely
columns_to_drop = ['assignment_name']
existing_columns_to_drop = [col for col in columns_to_drop if col in df.columns]
df = df.drop(columns=existing_columns_to_drop)

# Exclude project_id from features
df = df.drop(columns=['project_id'], errors='ignore')

# Convert all columns to numeric and handle non-numeric values
df = df.apply(pd.to_numeric, errors='coerce')

# Handle missing values
df = df.fillna(df.mean())

# Print data after preprocessing
print("Data after preprocessing:")
print(df.head())

# Separate features and target variable
X = df.drop('current_progress', axis=1, errors='ignore')
y = df['current_progress']

# Print shapes of features and target variable
print("Features (X) shape:", X.shape)
print("Target (y) shape:", y.shape)

# Train the model if there is data
if X.shape[0] > 0 and y.shape[0] > 0:
    # Split the data
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    # Train the model
    model = RandomForestClassifier()
    model.fit(X_train, y_train)

    # Evaluate the model
    predictions = model.predict(X_test)
    accuracy = accuracy_score(y_test, predictions)
    print(f"Model Accuracy: {accuracy}")

    # Save the model
    joblib.dump(model, 'model.pkl')
else:
    print("Not enough data to train the model.")

# Load the trained model for predictions
model = joblib.load('model.pkl')

# Connect to the database for predictions
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="fyp_progress_recorder"
)
cursor = conn.cursor(dictionary=True)

# Fetch all data from the training_data table
query = "SELECT * FROM training_data"
cursor.execute(query)
data = pd.DataFrame(cursor.fetchall())

# Print raw data for debugging
print("Raw data from database:")
print(data.head())

# Convert categorical columns to numeric
label_columns = ['assignment_status']
for col in label_columns:
    if col in data.columns:
        le = LabelEncoder()
        data[col] = le.fit_transform(data[col].astype(str))

# Convert feedback columns to numeric
feedback_mapping = {
    'Excellent': 5,
    'Good': 4,
    'Average': 3,
    'Fair': 2,
    'Poor': 1
}
data['avg_meeting_feedback'] = data['avg_meeting_feedback'].map(feedback_mapping)
data['avg_presentation_feedback'] = data['avg_presentation_feedback'].map(feedback_mapping)

# Drop unnecessary columns if they exist
columns_to_drop = ['assignment_name', 'assignment_deadline', 'degree_program', 'academic_year']
existing_columns_to_drop = [col for col in columns_to_drop if col in data.columns]
data = data.drop(columns=existing_columns_to_drop)

# Convert project_id to string for consistent handling
data['project_id'] = data['project_id'].astype(str)

# Exclude project_id from features for prediction
data_features = data.drop(columns=['project_id', 'current_progress'], errors='ignore')

# Convert all columns to numeric and handle non-numeric values
data_features = data_features.apply(pd.to_numeric, errors='coerce')

# Handle missing values
data_features = data_features.fillna(data_features.mean())

# Print processed data for debugging
print("Processed Data for Prediction:")
print(data_features.head())

# Prepare data for prediction
X_all = data_features

# Make predictions
predictions = model.predict(X_all)
data['predictions'] = predictions

# Print predictions
print("Predictions for All Projects:")
print(data[['project_id', 'predictions']])

# Ensure project_id is not NaN and is of correct type
data = data.dropna(subset=['project_id'])
data['predictions'] = data['predictions'].astype(float)

# Print data to be inserted for debugging
print("Data to be inserted into database:")
print(data[['project_id', 'predictions']])

# Insert predictions into the database
for i, row in data.iterrows():
    project_id = row['project_id']
    predicted_progress = row['predictions']
    print(f"Inserting: project_id={project_id}, predicted_progress={predicted_progress}")
    query = """
    INSERT INTO project_predictions (project_id, predicted_progress) 
    VALUES (%s, %s)
    ON DUPLICATE KEY UPDATE predicted_progress = VALUES(predicted_progress)
    """
    try:
        cursor.execute(query, (project_id, predicted_progress))
        conn.commit()
    except mysql.connector.Error as e:
        print(f"Error inserting data: {e}")
        continue

print("Predictions updated successfully.")

# Close the connection
cursor.close()
conn.close()

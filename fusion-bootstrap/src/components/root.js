import React, { useState } from 'react';

import { Helmet } from 'fusion-plugin-react-helmet-async';
import { DisplayLocations } from './locations';

const HelmetComponent = () => (
  <Helmet>
    <style>
      {`
        body {
          background-color: #f5f5f5;
          font: 24px 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        h1 {
          color: rgba(175, 47, 47, 0.15);
          font-size: 100px;
          font-weight: 100;
          text-align: center;
        }
        .container {
          background: #ffffff;
          border: 1px solid #ededed;
          margin: 0 auto;
          width: 550px;
        }
        input {
          border: none;
          font-size: 24px;
          font-weight: 300;
          padding: 15px;
          width: 520px;
        }
        input::placeholder {
          color: #e6e6e6;
          font-style: italic;
          font-weight: 100;
        }
        .todo {
          border-top: 1px solid #ededed;
          padding: 15px;
        }
        .todo-text {
          font-weight: 300;
        }
      `}
    </style>
  </Helmet>
);

const Root = () => {
  const [todos, setTodos] = useState([]);
  const [inputText, setInputText] = useState('');

  const handleOnKeydown = (e) => {
    if (e.key === 'Enter') {
      setTodos([...todos, inputText]);
      setInputText('');
    }
  };

  const handleOnChange = (e) => {
    setInputText(e.target.value);
  };

  return (
    <>
      <HelmetComponent />
      <h1>Todos</h1>
      <div className="container">
        <input
          type="text"
          onChange={handleOnChange}
          onKeyDown={handleOnKeydown}
          placeholder="What needs to be done?"
          value={inputText}
        />

        <DisplayLocations />
        {/* {todos.map((todo, index) => (
          <div key={index}>
            <div className="todo-text">{todo}</div>
          </div>
        ))} */}
      </div>
    </>
  );
};

export default Root;

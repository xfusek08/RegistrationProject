<?php
class DataType
{
  const None = 0;
  const String = 1;
  const Integer = 2;
  const Float = 3;
  const Date = 4;
  const DateTrnc = 5;
  const Timestamp = 6;
  const Bool = 7;
}
class Operator
{
  const Equal = 0;
  const IsBigger = 1;
  const IsBiggerEg = 2;
  const IsSmaller = 3;
  const IsSmallerEq = 4;
}
class SaveToDBResult
{
  const None = 0;
  const OK = 1;
  const Error = 2;
  const InvalidData = 3;
}
